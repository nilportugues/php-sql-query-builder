<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Syntax;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;

/**
 * Class OrderByTest.
 */
class OrderByTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $columnClass = '\NilPortugues\Sql\QueryBuilder\Syntax\Column';

    /**
     * @test
     */
    public function itShouldConstructOrderBy()
    {
        $column = new Column('registration_date', 'user');
        $order = new OrderBy($column, OrderBy::ASC);

        $this->assertInstanceOf($this->columnClass, $order->getColumn());
        $this->assertEquals(OrderBy::ASC, $order->getDirection());
    }

    /**
     * @test
     */
    public function itShouldGetOrderByDirection()
    {
        $column = new Column('registration_date', 'user');
        $order = new OrderBy($column, OrderBy::ASC);

        $this->assertEquals(OrderBy::ASC, $order->getDirection());

        $order->setDirection(OrderBy::DESC);
        $this->assertEquals(OrderBy::DESC, $order->getDirection());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionIfDirectionNotValid()
    {
        $column = new Column('registration_date', 'user');
        $order = new OrderBy($column, OrderBy::ASC);

        $this->setExpectedException('\InvalidArgumentException');
        $order->setDirection('this is not a valid direction');
    }
}
