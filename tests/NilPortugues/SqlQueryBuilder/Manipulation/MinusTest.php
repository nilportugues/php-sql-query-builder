<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:26 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Manipulation\Minus;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class MinusTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
 */
class MinusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Minus
     */
    private $query;

    /**
     * @var string
     */
    private $exceptionClass = '\NilPortugues\SqlQueryBuilder\Manipulation\QueryException';

    /**
     *
     */
    protected function setUp()
    {
        $this->query  = new Minus(new Select('user'), new Select('user_email'));
    }

    /**
     * @test
     */
    public function it_should_get_part_name()
    {
        $this->assertSame('MINUS', $this->query->partName());
    }

    /**
     * @test
     */
    public function it_should_throw_exception_for_unsupported_get_table()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->getTable();
    }

    /**
     * @test
     */
    public function it_should_throw_exception_for_unsupported_get_where()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->getWhere();
    }

    /**
     * @test
     */
    public function it_should_throw_exception_for_unsupported_where()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->where();
    }

    /**
     * @test
     */
    public function it_should_get_minus_selects()
    {
        $this->assertEquals(new Select('user'), $this->query->getFirst());
        $this->assertEquals(new Select('user_email'), $this->query->getSecond());
    }
}
