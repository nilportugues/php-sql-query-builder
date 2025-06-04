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
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use PHPUnit\Framework\TestCase;

/**
 * Class InsertWriterTest.
 */
class InsertWriterTest extends TestCase
{
    private GenericBuilder $writer;
    private Insert $query;
    private string $exceptionClass = QueryException::class;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->query = new Insert();
    }

    /**
     * @test
     */
    public function itShouldThrowQueryExceptionBecauseNoColumnsWereDefined(): void
    {
        $this->expectException($this->exceptionClass);
        $this->expectExceptionMessage('No columns were defined for the current schema.');

        $this->query->setTable('user');
        $this->writer->write($this->query);
    }

    /**
     * @test
     */
    public function itShouldWriteInsertQuery(): void
    {
        $valueArray = [
            'user_id' => 1,
            'name' => 'Nil',
            'contact' => 'contact@nilportugues.com',
        ];

        $this->query
            ->setTable('user')
            ->setValues($valueArray);

        $expectedSQL = 'INSERT INTO user (user.user_id, user.name, user.contact) VALUES (:v1, :v2, :v3)';

        $this->assertSame($expectedSQL, $this->writer->write($this->query));
        // Query::getValues() returns the raw values, GenericBuilder::getValues() returns placeholder values
        $this->assertEquals(\array_values($valueArray), \array_values($this->query->getValues()));

        $expectedPlaceholders = [':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com'];
        $this->assertEquals($expectedPlaceholders, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteCommentInQuery(): void
    {
        $valueArray = [
            'user_id' => 1,
            'name' => 'Nil',
            'contact' => 'contact@nilportugues.com',
        ];

        $this->query
            ->setTable('user')
            ->setComment('This is a comment')
            ->setValues($valueArray);

        $expectedSQL = "-- This is a comment\n" . 'INSERT INTO user (user.user_id, user.name, user.contact) VALUES (:v1, :v2, :v3)';

        $this->assertSame($expectedSQL, $this->writer->write($this->query));
        $this->assertEquals(\array_values($valueArray), \array_values($this->query->getValues()));

        $expectedPlaceholders = [':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com'];
        $this->assertEquals($expectedPlaceholders, $this->writer->getValues());
    }
}
