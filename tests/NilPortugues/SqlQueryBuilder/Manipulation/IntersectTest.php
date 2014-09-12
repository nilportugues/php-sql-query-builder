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

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Intersect;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class IntersectTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
 */
class IntersectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var Intersect
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
        $this->writer = new GenericBuilder();
        $this->query  = new Intersect();
    }

    /**
     * @test
     */
    public function it_should_throw_exception_for_unsupported_operations()
    {
        $this->setExpectedException($this->exceptionClass);
        $this->query->getTable();

        $this->setExpectedException($this->exceptionClass);
        $this->query->getWhere();

        $this->setExpectedException($this->exceptionClass);
        $this->query->where();
    }

    /**
     * @test
     */
    public function it_should_get_intersect_selects()
    {
        $this->assertEquals(array(), $this->query->getIntersects());

        $select1 = new Select('user');
        $select2 = new Select('user_email');

        $this->query->add($select1);
        $this->query->add($select2);

        $this->assertEquals(array($select1, $select2), $this->query->getIntersects());
    }
}
