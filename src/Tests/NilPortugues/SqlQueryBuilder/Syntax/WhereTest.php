<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:31 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Syntax;

use NilPortugues\SqlQueryBuilder\Syntax\Where;
use Tests\NilPortugues\SqlQueryBuilder\Manipulation\Resources\DummyQuery;

/**
 * Class WhereTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Syntax
 */
class WhereTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Where
     */
    protected $where;

    /**
     * @var string
     */
    protected $whereClass = '\NilPortugues\SqlQueryBuilder\Syntax\Where';

    /**
     * @var string
     */
    protected $columnClass = '\NilPortugues\SqlQueryBuilder\Syntax\Column';

    /**
     * @var string
     */
    protected $queryException = '\NilPortugues\SqlQueryBuilder\Manipulation\QueryException';

    /**
     *
     */
    protected function setUp()
    {
        $query = new DummyQuery();
        $query->setTable('users');

        $this->where = new Where($query);
    }

    /**
     * @test
     */
    public function it_should_be_clonable()
    {
        $this->assertEquals($this->where, clone $this->where);
    }

    /**
     * @test
     */
    public function it_should_be_empty_on_construct()
    {
        $this->assertEmpty($this->where->isEmpty());
    }

    /**
     * @test
     */
    public function it_should_return_default_conjuction_AND()
    {
        $this->assertSame('AND', $this->where->getConjunction());
    }

    /**
     * @test
     */
    public function it_should_return_default_sub_where()
    {
        $this->assertSame(array(), $this->where->getSubWheres());
    }

    /**
     * @test
     */
    public function it_should_return_sub_filter()
    {
        $filter = $this->where->subWhere();

        $this->assertSame(array(), $filter->getSubWheres());
        $this->assertInstanceOf($this->whereClass, $filter);
    }

    /**
     * @test
     */
    public function it_should_return_the_same_eq_and_equal()
    {
        $column = 'user_id';
        $value  = 1;

        $this->assertSame(
            $this->where->equals($column, $value),
            $this->where->eq($column, $value)
        );
    }

    /**
     * @test
     */
    public function it_should_not_be_equal_to()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->notEquals($column, $value)->getComparisons();

        $this->assertSame('<>', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_be_greater_than()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->greaterThan($column, $value)->getComparisons();

        $this->assertSame('>', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_be_greater_than_or_equal()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->greaterThanOrEqual($column, $value)->getComparisons();

        $this->assertSame('>=', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_be_less_than()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->lessThan($column, $value)->getComparisons();

        $this->assertSame('<', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_be_less_than_or_equal()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->lessThanOrEqual($column, $value)->getComparisons();

        $this->assertSame('<=', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_be_like()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->like($column, $value)->getComparisons();

        $this->assertSame('LIKE', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_be_not_like()
    {
        $column = 'user_id';
        $value  = 1;

        $result = $this->where->notLike($column, $value)->getComparisons();

        $this->assertSame('NOT LIKE', $result[0]['conjunction']);
        $this->assertSame($column, $result[0]['subject']->getName());
        $this->assertSame($value, $result[0]['target']);
    }

    /**
     * @test
     */
    public function it_should_accumulate_match_conditions()
    {
        $column = array('user_id');

        $result = $this->where
            ->match($column, array(1, 2, 3))
            ->getMatches();

        $expected = array(
            0 => array(
                'columns' => array('user_id'),
                'values'  => array(1, 2, 3),
                'mode'    => 'natural'
            )
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_accumulate_match_boolean_conditions()
    {
        $column = array('user_id');

        $result = $this->where
            ->matchBoolean($column, array(1, 2, 3))
            ->getMatches();

        $expected = array(
            0 => array(
                'columns' => array('user_id'),
                'values'  => array(1, 2, 3),
                'mode'    => 'boolean'
            )
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_accumulate_match_query_expansion_conditions()
    {
        $column = array('user_id');

        $result = $this->where
            ->matchWithQueryExpansion($column, array(1, 2, 3))
            ->getMatches();

        $expected = array(
            0 => array(
                'columns' => array('user_id'),
                'values'  => array(1, 2, 3),
                'mode'    => 'query_expansion'
            )
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_accumulate_in_conditions()
    {
        $column = 'user_id';

        $result = $this->where
            ->in($column, array(1, 2, 3))
            ->getIns();

        $expected = array($column => array(1, 2, 3));
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_accumulate_not_in_conditions()
    {
        $column = 'user_id';

        $result = $this->where
            ->notIn($column, array(1, 2, 3))
            ->getNotIns();

        $expected = array($column => array(1, 2, 3));
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_write_between_conditions()
    {
        $column = 'user_id';

        $result = $this->where
            ->between($column, 1, 2)
            ->getBetweens();

        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
        $this->assertEquals(1, $result[0]['a']);
        $this->assertEquals(2, $result[0]['b']);
    }

    /**
     * @test
     */
    public function it_should_set_null_value_condition()
    {
        $column = 'user_id';

        $result = $this->where
            ->isNull($column)
            ->getNull();

        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
    }

    /**
     * @test
     */
    public function it_should_set_is_not_null_value_condition()
    {
        $column = 'user_id';

        $result = $this->where
            ->isNotNull($column)
            ->getNotNull();

        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
    }

    /**
     * @test
     */
    public function it_should_set_bit_clause_value_condition()
    {
        $column = 'user_id';

        $result = $this->where
            ->addBitClause($column, 1)
            ->getBooleans();

        $this->assertEquals(1, $result[0]['value']);
        $this->assertInstanceOf($this->columnClass, $result[0]['subject']);
    }

    /**
     * @test
     */
    public function it_should_change_AND_to_OR_operator()
    {
        $result = $this->where->conjunction('OR');
        $this->assertEquals('OR', $result->getConjunction());
    }

    /**
     * @test
     */
    public function it_should_throw_exception_on_unknown_conjunction()
    {
        $this->setExpectedException($this->queryException);
        $this->where->conjunction('NOT_VALID_CONJUNCTION');
    }
}
