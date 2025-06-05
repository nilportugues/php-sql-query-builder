<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/16/14
 * Time: 8:56 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Intersect;
use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Union;
use NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use PHPUnit\Framework\TestCase;

class GenericBuilderTest extends TestCase
{
    private GenericBuilder $writer;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
    }

    /**
     * @test
     */
    public function itShouldCreateSelectObject(): void
    {
        $this->assertInstanceOf(Select::class, $this->writer->select());
    }

    /**
     * @test
     */
    public function itShouldCreateInsertObject(): void
    {
        $this->assertInstanceOf(Insert::class, $this->writer->insert());
    }

    /**
     * @test
     */
    public function itShouldCreateUpdateObject(): void
    {
        $this->assertInstanceOf(Update::class, $this->writer->update());
    }

    /**
     * @test
     */
    public function itShouldCreateDeleteObject(): void
    {
        $this->assertInstanceOf(Delete::class, $this->writer->delete());
    }

    /**
     * @test
     */
    public function itShouldCreateIntersectObject(): void
    {
        $this->assertInstanceOf(Intersect::class, $this->writer->intersect());
    }

    /**
     * @test
     */
    public function itShouldCreateMinusObject(): void
    {
        $select1 = new Select('table1');
        $select1->setBuilder($this->writer);
        $select2 = new Select('table2');
        $select2->setBuilder($this->writer);
        $this->assertInstanceOf(Minus::class, $this->writer->minus($select1, $select2));
    }

    /**
     * @test
     */
    public function itShouldCreateUnionObject(): void
    {
        $this->assertInstanceOf(Union::class, $this->writer->union());
    }

    /**
     * @test
     */
    public function itShouldCreateUnionAllObject(): void
    {
        $this->assertInstanceOf(UnionAll::class, $this->writer->unionAll());
    }

    /**
     * @test
     */
    public function itCanAcceptATableNameForSelectInsertUpdateDeleteQueries(): void
    {
        $table = 'user';
        $queries = [
            'select' => $this->writer->select($table),
            'insert' => $this->writer->insert($table),
            'update' => $this->writer->update($table),
            'delete' => $this->writer->delete($table),
        ];

        foreach ($queries as $type => $query) {
            $this->assertNotNull($query->getTable(), "Table should not be null for $type query");
            $this->assertEquals($table, $query->getTable()->getName(), "Checking table in $type query");
        }
    }

    /**
     * @test
     */
    public function itCanAcceptATableAndColumnsForSelect(): void
    {
        $table = 'user';
        $columns = ['id', 'role'];
        $expected = <<<QUERY
SELECT
    user.id,
    user.role
FROM
    user

QUERY;

        $select = $this->writer->select($table, $columns);
        $this->assertSame(str_replace("\r\n", "\n", $expected), $this->writer->writeFormatted($select));
    }

    /**
     * @test
     */
    public function itCanAcceptATableAndValuesForInsert(): void
    {
        $table = 'user';
        $values = ['id' => 1, 'role' => 'admin'];
        $expected = <<<QUERY
INSERT INTO user (user.id, user.role)
VALUES
    (:v1, :v2)

QUERY;

        $insert = $this->writer->insert($table, $values);
        $this->assertSame(str_replace("\r\n", "\n", $expected), $this->writer->writeFormatted($insert));
    }

    /**
     * @test
     */
    public function itCanAcceptATableAndValuesForUpdate(): void
    {
        $table = 'user';
        $values = ['id' => 1, 'role' => 'super-admin'];
        $expected = <<<QUERY
UPDATE
    user
SET
    user.id = :v1,
    user.role = :v2

QUERY;

        $update = $this->writer->update($table, $values);
        $this->assertSame(str_replace("\r\n", "\n", $expected), $this->writer->writeFormatted($update));
    }

    /**
     * @test
     */
    public function itShouldOutputHumanReadableQuery(): void
    {
        $selectRole = $this->writer->select(); // Builder is set by GenericBuilder::select
        $selectRole
            ->setTable('role')
            ->setColumns(['role_name'])
            ->limit(1)
            ->where()
            ->equals('role_id', 3);

        $select = $this->writer->select(); // Builder is set by GenericBuilder::select
        $select->setTable('user')
            ->setColumns(['user_id', 'username'])
            ->setSelectAsColumn(['user_role' => $selectRole])
            // For this sub-select used as a column, if it needs its own builder context for complex operations
            // before being passed to setSelectAsColumn, it should also have its builder set.
            // However, GenericBuilder::write will set builder on the main $select.
            // The sub-select's SQL is generated when the main $select is written.
            ->setSelectAsColumn([$selectRole])
            ->where()
            ->equals('user_id', 4);

        $expected = <<<QUERY
SELECT
    user.user_id,
    user.username,
    (
        SELECT
            role.role_name
        FROM
            role
        WHERE
            (role.role_id = :v1)
        LIMIT
            :v2,
            :v3
    ) AS "user_role",
    (
        SELECT
            role.role_name
        FROM
            role
        WHERE
            (role.role_id = :v4)
        LIMIT
            :v5,
            :v6
    ) AS "role"
FROM
    user
WHERE
    (user.user_id = :v7)

QUERY;

        $this->assertSame(str_replace("\r\n", "\n", $expected), $this->writer->writeFormatted($select));
    }

    /**
     * @test
     */
    public function it_should_inject_the_builder(): void
    {
        $query = $this->writer->select();
        $this->assertSame($this->writer, $query->getBuilder());
    }

    /**
     * @test
     */
    public function itShouldWriteWhenGettingSql(): void
    {
        $query = $this->writer->select()
            ->setTable('user');
        // $query->setBuilder($this->writer); // Already set by $this->writer->select()
        $expected = $this->writer->write($query);
        $this->assertSame($expected, $query->getSql());
    }

    /**
     * @test
     */
    public function itShouldWriteFormattedWhenGettingFormattedSql(): void
    {
        $query = $this->writer->select()
            ->setTable('user');
        // $query->setBuilder($this->writer); // Already set
        $expected = $this->writer->writeFormatted($query);
        $this->assertSame($expected, $query->getSql(true));
    }
    /**
     * @test
     */
    public function itShouldWriteSqlWhenCastToString(): void
    {
        $query = $this->writer->select()
            ->setTable('user');
        // $query->setBuilder($this->writer); // Already set
        $expected = $this->writer->write($query);
        $this->assertSame($expected, (string) $query);
    }
}
