<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:31 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder; // For setBuilder
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use NilPortugues\Tests\Sql\QueryBuilder\Manipulation\Resources\DummyQuery;
use PHPUnit\Framework\TestCase;

/**
 * Class WhereTest.
 */
class WhereTest extends TestCase
{
    protected Where $where;
    protected string $whereClass = Where::class;
    protected string $columnClass = Column::class;
    protected string $queryExceptionClass = QueryException::class; // Renamed for clarity

    protected function setUp(): void
    {
        $query = new DummyQuery();
        $query->setTable('users');
        $query->setBuilder(new GenericBuilder()); // DummyQuery needs a builder

        $this->where = new Where($query);
    }

    /**
     * @test
     */
    public function itShouldBeCloneable(): void
    {
        $this->assertEquals($this->where, clone $this->where);
    }

    /**
     * @test
     */
    public function itShouldBeEmptyOnConstruct(): void
    {
        $this->assertTrue($this->where->isEmpty());
    }

    /**
     * @test
     */
    public function itShouldReturnDefaultConjuctionAnd(): void
    {
        $this->assertSame('AND', $this->where->getConjunction());
    }

    /**
     * @test
     */
    public function itShouldReturnDefaultSubWhere(): void
    {
        $this->assertSame([], $this->where->getSubWheres());
    }

    /**
     * @test
     */
    public function itShouldReturnSubFilter(): void
    {
        $filter = $this->where->subWhere();
        $this->assertSame([], $filter->getSubWheres());
        $this->assertInstanceOf($this->whereClass, $filter);
    }

    /**
     * @test
     */
    public function itShouldReturnTheSameEqAndEqual(): void
    {
        $column = 'user_id';
        $value = 1;

        // These methods return $this (the Where object), so they will be the same instance.
        $this->assertSame(
            $this->where->equals($column, $value),
            $this->where->eq($column, $value)
        );
    }

    /**
     * @test
     */
    public function itShouldNotBeEqualTo(): void
    {
        $column = 'user_id';
        $value = 1;
        $result = $this->where->notEquals($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('<>', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldBeGreaterThan(): void
    {
        $column = 'user_id';
        $value = 1;
        $result = $this->where->greaterThan($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('>', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldBeGreaterThanOrEqual(): void
    {
        $column = 'user_id';
        $value = 1;
        $result = $this->where->greaterThanOrEqual($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('>=', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldBeLessThan(): void
    {
        $column = 'user_id';
        $value = 1;
        $result = $this->where->lessThan($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('<', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldBeLessThanOrEqual(): void
    {
        $column = 'user_id';
        $value = 1;
        $result = $this->where->lessThanOrEqual($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('<=', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldBeLike(): void
    {
        $column = 'user_id';
        $value = 1; // Or a string like '%pattern%'
        $result = $this->where->like($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('LIKE', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldBeNotLike(): void
    {
        $column = 'user_id';
        $value = 1; // Or a string
        $result = $this->where->notLike($column, $value)->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertSame('NOT LIKE', $result[0]['conjunction']);
        $this->assertInstanceOf(Column::class, $result[0]['subject']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function itShouldAccumulateMatchConditions(): void
    {
        $columns = ['user_id'];
        $values = [1, 2, 3];
        $result = $this->where->match($columns, $values)->getMatches();
        $expected = [
            0 => ['columns' => $columns, 'values' => $values, 'mode' => 'natural'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itShouldAccumulateMatchBooleanConditions(): void
    {
        $columns = ['user_id'];
        $values = [1, 2, 3];
        $result = $this->where->matchBoolean($columns, $values)->getMatches();
        $expected = [
            0 => ['columns' => $columns, 'values' => $values, 'mode' => 'boolean'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itShouldAccumulateMatchQueryExpansionConditions(): void
    {
        $columns = ['user_id'];
        $values = [1, 2, 3];
        $result = $this->where->matchWithQueryExpansion($columns, $values)->getMatches();
        $expected = [
            0 => ['columns' => $columns, 'values' => $values, 'mode' => 'query_expansion'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itShouldAccumulateInConditions(): void
    {
        $column = 'user_id';
        $values = [1, 2, 3];
        $result = $this->where->in($column, $values)->getIns();
        $expected = [$column => $values];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itShouldAccumulateNotInConditions(): void
    {
        $column = 'user_id';
        $values = [1, 2, 3];
        $result = $this->where->notIn($column, $values)->getNotIns();
        $expected = [$column => $values];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itShouldWriteBetweenConditions(): void
    {
        $column = 'user_id';
        $result = $this->where->between($column, 1, 2)->getBetweens();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
        $this->assertEquals(1, $result[0]['a']);
        $this->assertEquals(2, $result[0]['b']);
    }

    /**
     * @test
     */
    public function itShouldSetNullValueCondition(): void
    {
        $column = 'user_id';
        $result = $this->where->isNull($column)->getNull();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
    }

    /**
     * @test
     */
    public function itShouldSetIsNotNullValueCondition(): void
    {
        $column = 'user_id';
        $result = $this->where->isNotNull($column)->getNotNull();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
    }

    /**
     * @test
     */
    public function itShouldSetBitClauseValueCondition(): void
    {
        $column = 'user_id';
        $result = $this->where->addBitClause($column, 1)->getBooleans();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result[0]);
        $this->assertEquals(1, $result[0]['value']);
        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
    }

    /**
     * @test
     */
    public function ItShouldChangeAndToOrOperator(): void
    {
        $result = $this->where->conjunction('OR');
        $this->assertEquals('OR', $result->getConjunction());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionOnUnknownConjunction(): void
    {
        $this->expectException($this->queryExceptionClass);
        $this->where->conjunction('NOT_VALID_CONJUNCTION');
    }

    /**
     * @test
     */
    public function itShouldSetExistsCondition(): void
    {
        $builder = new GenericBuilder();
        $select1 = new Select('user');
        $select1->setBuilder($builder);
        $select1->where()->equals('user_id', 10);

        $result = $this->where->exists($select1)->getExists();
        $this->assertEquals([$select1], $result);
    }

    /**
     * @test
     */
    public function itShouldSetNotExistsCondition(): void
    {
        $builder = new GenericBuilder();
        $select1 = new Select('user');
        $select1->setBuilder($builder);
        $select1->where()->equals('user_id', 10);

        $result = $this->where->notExists($select1)->getNotExists();
        $this->assertEquals([$select1], $result);
    }

    /**
     * @test
     */
    public function itShouldReturnLiterals(): void
    {
        $result = $this->where->asLiteral('(username is not null and status=:status)')->getComparisons();
        $this->assertNotEmpty($result);
        $this->assertSame('(username is not null and status=:status)', $result[0]);
    }
}
