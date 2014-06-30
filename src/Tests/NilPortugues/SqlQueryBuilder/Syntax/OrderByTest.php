<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Syntax;

use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\OrderBy;

/**
 * Class OrderByTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Syntax
 */
class OrderByTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $columnClass = '\NilPortugues\SqlQueryBuilder\Syntax\Column';

    /**
     * @test
     */
    public function it_should_construct_order_by()
    {
        $column = new Column("registration_date", "user");
        $order  = new OrderBy($column, OrderBy::ASC);

        $this->assertInstanceOf($this->columnClass, $order->getColumn());
        $this->assertEquals(OrderBy::ASC, $order->getDirection());
    }

    /**
     * @test
     */
    public function it_should_get_order_by_direction()
    {
        $column = new Column("registration_date", "user");
        $order  = new OrderBy($column, OrderBy::ASC);

        $this->assertEquals(OrderBy::ASC, $order->getDirection());

        $order->setDirection(OrderBy::DESC);
        $this->assertEquals(OrderBy::DESC, $order->getDirection());
    }

    /**
     * @test
     */
    public function it_should_throw_exception_if_direction_not_valid()
    {
        $column = new Column("registration_date", "user");
        $order  = new OrderBy($column, OrderBy::ASC);

        $this->setExpectedException('\InvalidArgumentException');
        $order->setDirection("this is not a valid direction");
    }
}
