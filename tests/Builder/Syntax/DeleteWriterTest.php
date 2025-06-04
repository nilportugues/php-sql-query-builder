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
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteWriterTest.
 */
class DeleteWriterTest extends TestCase
{
    private GenericBuilder $writer;
    private Delete $query;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->query = new Delete();
    }

    /**
     * @test
     */
    public function itShouldWriteDeleteAllTableContentsQuery(): void
    {
        $this->query->setTable('user');

        $expected = 'DELETE FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldWriteDeleteRowLimit1(): void
    {
        $this->query
            ->setTable('user')
            ->limit(1);

        $expected = 'DELETE FROM user LIMIT :v1';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteCommentInQuery(): void
    {
        $this->query
            ->setTable('user')
            ->setComment('This is a comment');

        $expected = <<<SQL
-- This is a comment
DELETE FROM user
SQL;
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldWriteDeleteRowWithWhereConditionAndLimit1(): void
    {
        $this->query->setTable('user');

        $conditions = $this->query->where();
        $conditions
            ->equals('user_id', 10)
            ->equals('user_id', 20)
            ->equals('user_id', 30);

        $this->query->limit(1);

        $expected = <<<SQL
DELETE FROM user WHERE (user.user_id = :v1) AND (user.user_id = :v2) AND (user.user_id = :v3) LIMIT :v4
SQL;
        // Normalize newlines for comparison, as Heredoc might behave differently across OS or configurations.
        $this->assertSame(str_replace("\r\n", "\n", $expected), $this->writer->write($this->query));


        $expectedValues = [':v1' => 10, ':v2' => 20, ':v3' => 30, ':v4' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }
}
