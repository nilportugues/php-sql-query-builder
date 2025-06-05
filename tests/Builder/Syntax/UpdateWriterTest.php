<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:47 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use PHPUnit\Framework\TestCase;

/**
 * Class UpdateWriterTest.
 */
class UpdateWriterTest extends TestCase
{
    /** @var array<string,mixed> */
    private array $valueArray = [];
    private GenericBuilder $writer;
    private Update $query;
    private string $exceptionClass = QueryException::class;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->query = new Update();

        $this->valueArray = [
            'user_id' => 1,
            'name' => 'Nil',
            'contact' => 'contact@nilportugues.com',
        ];
    }

    /**
     * @test
     */
    public function itShouldThrowQueryException(): void
    {
        $this->expectException($this->exceptionClass);
        // The message in QueryException from UpdateWriter is 'No values to update in Update query.'
        $this->expectExceptionMessage('No values to update in Update query.');


        $this->query->setTable('user');
        $this->writer->write($this->query); // Fails because values are not set
    }

    /**
     * @test
     */
    public function itShouldWriteUpdateQuery(): void
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray);

        $expectedSQL = 'UPDATE user SET user.user_id = :v1, user.name = :v2, user.contact = :v3';
        $this->assertSame($expectedSQL, $this->writer->write($this->query));

        $expectedPlaceholders = [':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com'];
        $this->assertEquals($expectedPlaceholders, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteCommentInQuery(): void
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->setComment('This is a comment');

        $expectedSQL = <<<SQL
-- This is a comment
UPDATE user SET user.user_id = :v1, user.name = :v2, user.contact = :v3
SQL;
        $this->assertSame(str_replace("\r\n", "\n", $expectedSQL), $this->writer->write($this->query));

        $expectedPlaceholders = [':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com'];
        $this->assertEquals($expectedPlaceholders, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldWriteUpdateQueryWithWhereConstrain(): void
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->where()
            ->equals('user_id', 1);

        $expectedSQL = 'UPDATE user SET user.user_id = :v1, user.name = :v2, user.contact = :v3 WHERE (user.user_id = :v4)';
        $this->assertSame($expectedSQL, $this->writer->write($this->query));

        $expectedPlaceholders = [':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1];
        $this->assertEquals($expectedPlaceholders, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldWriteUpdateQueryWithWhereConstrainAndLimit1(): void
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->where()
            ->equals('user_id', 1);

        $this->query->limit(1);

        $expectedSQL = 'UPDATE user SET user.user_id = :v1, user.name = :v2, user.contact = :v3 WHERE (user.user_id = :v4) LIMIT :v5';
        $this->assertSame($expectedSQL, $this->writer->write($this->query));

        $expectedPlaceholders = [':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1, ':v5' => 1];
        $this->assertEquals($expectedPlaceholders, $this->writer->getValues());
    }
}
