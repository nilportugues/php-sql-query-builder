<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:45 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\ColumnWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use PHPUnit\Framework\TestCase;

/**
 * Class ColumnWriterTest.
 */
class ColumnWriterTest extends TestCase
{
    private ColumnWriter $columnWriter;
    private GenericBuilder $writer;
    private Select $query;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->query = new Select(); // No args needed as per Select constructor update
        $this->columnWriter = new ColumnWriter(new GenericBuilder(), new PlaceholderWriter());
    }

    /**
     * @test
     */
    public function itShouldWriteColumn(): void
    {
        $column = new Column('user_id', 'user');
        $result = $this->columnWriter->writeColumn($column);
        $this->assertSame('user.user_id', $result);
    }

    /**
     * @test
     */
    public function itShouldWriteValueAsColumns(): void
    {
        $select = new Select('user');
        $select->setValueAsColumn('1', 'user_id');
        $result = $this->columnWriter->writeValueAsColumns($select);
        $this->assertInstanceOf(Column::class, $result[0]);
    }

    /**
     * @test
     */
    public function itShouldWriteFuncAsColumns(): void
    {
        $select = new Select('user');
        $select->setFunctionAsColumn('MAX', ['user_id'], 'max_value');
        $result = $this->columnWriter->writeFuncAsColumns($select);
        $this->assertInstanceOf(Column::class, $result[0]);
    }

    /**
     * @test
     */
    public function itShouldWriteColumnWithAlias(): void
    {
        $column = new Column('user_id', 'user', 'userId');
        $result = $this->columnWriter->writeColumnWithAlias($column);
        $this->assertSame('user.user_id AS "userId"', $result);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteColumnAsASelectStatement(): void
    {
        $selectRole = new Select();
        $selectRole
            ->setTable('role')
            ->setColumns(['role_name'])
            ->limit(1, 0) // Assuming limit(offset, count) or limit(count)
            ->where()
            ->equals('role_id', 3);

        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'username'])
            ->setSelectAsColumn(['user_role' => $selectRole])
            ->setSelectAsColumn([$selectRole]) // This would likely use a generated alias or the select's table name
            ->where()
            ->equals('user_id', 4);

        // The alias for the second $selectRole will depend on how ColumnWriter handles it.
        // Based on current src/Syntax/ColumnWriter, and src/Syntax/SyntaxFactory::createColumn
        // a select object used as a column without an explicit alias will result in an alias derived
        // from its table name if the key is numeric. Let's assume it becomes "role".
        $expected = 'SELECT user.user_id, user.username, ' .
            '(SELECT role.role_name FROM role WHERE (role.role_id = :v1) LIMIT :v2, :v3) AS "user_role", ' .
            '(SELECT role.role_name FROM role WHERE (role.role_id = :v4) LIMIT :v5, :v6) AS "role" ' .
            'FROM user WHERE (user.user_id = :v7)';

        $this->assertSame($expected, $this->writer->write($this->query));

        // Placeholder count might change based on how limit is handled (0 or 1 for count, 0 for offset)
        // Original test had limit(1) which implies offset 1, count 0 if it's (start, count)
        // If limit(N) means N rows, and limit(S,C) means S offset, C rows.
        // GenericBuilder's Select::limit($start, $count=0) - if $count is 0, it might be treated as "LIMIT $start".
        // Let's assume limit(1,0) means LIMIT 0 OFFSET 1 (0 rows).
        // Or if it's limit(count, offset), then LIMIT 1 OFFSET 0.
        // Given the placeholders :v2, :v3 and :v5, :v6, it implies two arguments for limit.
        // The Select::limit method sets $this->limitStart = $start; $this->limitCount = $count;
        // The SelectWriter::writeSelectLimit uses $this->placeholderWriter->add($select->getLimitStart()) and
        // $this->placeholderWriter->add($select->getLimitCount()); if mask is not "00".
        // If limit(1) was called, it's limit(1,0). So start=1, count=0.
        // This results in "LIMIT :vX, :vY".
        $expectedValues = [':v1' => 3, ':v2' => 1, ':v3' => 0, ':v4' => 3, ':v5' => 1, ':v6' => 0, ':v7' => 4];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteColumnAsAValueStatement(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'username'])
            ->setValueAsColumn('10', 'priority')
            ->where()
            ->equals('user_id', 1);

        $expected = 'SELECT user.user_id, user.username, :v1 AS "priority" FROM user WHERE (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => '10', ':v2' => 1]; // Value '10' will be a string
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteColumnAsAFuncWithBracketsStatement(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'username'])
            ->setFunctionAsColumn('MAX', ['user_id'], 'max_id')
            ->where()
            ->equals('user_id', 1);

        $expected = 'SELECT user.user_id, user.username, MAX(user_id) AS "max_id" FROM user WHERE (user.user_id = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteColumnAsAFuncWithoutBracketsStatement(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'username'])
            ->setFunctionAsColumn('CURRENT_TIMESTAMP', [], 'server_time')
            ->where()
            ->equals('user_id', 1);

        $expected = 'SELECT user.user_id, user.username, CURRENT_TIMESTAMP AS "server_time" FROM user WHERE (user.user_id = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }
}
