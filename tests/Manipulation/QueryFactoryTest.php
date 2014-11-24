<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/16/14
 * Time: 8:50 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Manipulation\QueryFactory;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class QueryFactoryTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
 */
class QueryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_create_select_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Select';
        $this->assertInstanceOf($className, QueryFactory::createSelect());
    }

    /**
     * @test
     */
    public function it_should_create_insert_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Insert';
        $this->assertInstanceOf($className, QueryFactory::createInsert());
    }

    /**
     * @test
     */
    public function it_should_create_update_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Update';
        $this->assertInstanceOf($className, QueryFactory::createUpdate());
    }

    /**
     * @test
     */
    public function it_should_create_delete_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Delete';
        $this->assertInstanceOf($className, QueryFactory::createDelete());
    }

    /**
     * @test
     */
    public function it_should_create_minus_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Minus';
        $this->assertInstanceOf($className, QueryFactory::createMinus(new Select('table1'), new Select('table2')));
    }

    /**
     * @test
     */
    public function it_should_create_union_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Union';
        $this->assertInstanceOf($className, QueryFactory::createUnion());
    }

    /**
     * @test
     */
    public function it_should_create_union_all_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\UnionAll';
        $this->assertInstanceOf($className, QueryFactory::createUnionAll());
    }

    /**
     * @test
     */
    public function it_should_create_where_object()
    {
        $mockClass = '\NilPortugues\SqlQueryBuilder\Manipulation\QueryInterface';

        $query = $this->getMockBuilder($mockClass)
            ->disableOriginalConstructor()
            ->getMock();

        $className = '\NilPortugues\SqlQueryBuilder\Syntax\Where';
        $this->assertInstanceOf($className, QueryFactory::createWhere($query));
    }
}
