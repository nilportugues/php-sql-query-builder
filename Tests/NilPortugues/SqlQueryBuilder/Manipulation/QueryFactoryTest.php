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
