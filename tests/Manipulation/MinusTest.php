<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:26 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class MinusTest.
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
    private $exceptionClass = '\NilPortugues\Sql\QueryBuilder\Manipulation\QueryException';

    /**
     *
     */
    protected function setUp()
    {
        $this->query = new Minus(new Select('user'), new Select('user_email'));
    }

    /**
     * @test
     */
    public function itShouldGetPartName()
    {
        $this->assertSame('MINUS', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedGetTable()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->getTable();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedGetWhere()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->getWhere();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedWhere()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->where();
    }

    /**
     * @test
     */
    public function itShouldGetMinusSelects()
    {
        $this->assertEquals(new Select('user'), $this->query->getFirst());
        $this->assertEquals(new Select('user_email'), $this->query->getSecond());
    }
}
