<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/2/14
 * Time: 11:34 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\NilPortugues\SqlQueryBuilder\Syntax;

use NilPortugues\SqlQueryBuilder\Syntax\Table;

/**
 * Class TableTest
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testConstruct()
    {
        $table = new Table("user");
        $this->assertEquals("user", $table->getName());
    }

    /**
     * @test
     */
    public function it_should_return_null_if_table_name_has_no_alias()
    {
        $table = new Table("user");
        $this->assertNull($table->getAlias());
    }

    /**
     * @test
     */
    public function it_should_return_alias_if_table_name_alias_has_been_set()
    {
        $table = new Table("user");
        $table->setAlias("u");
        $this->assertEquals("u", $table->getAlias());
    }

    /**
     * @test
     */
    public function it_should_return_null_if_schema_not_set()
    {
        $table = new Table("user");
        $this->assertNull($table->getSchema());
    }

    /**
     * @test
     */
    public function it_should_return_schema_if_schema_has_value()
    {
        $table = new Table("user", "website");
        $this->assertEquals("website", $table->getSchema());
    }

    /**
     * @test
     */
    public function it_should_return_the_complete_name()
    {
        $table = new Table("user");

        $table->setAlias("p");
        $table->setSchema("website");

        $this->assertEquals("website.user AS p", $table->getCompleteName());
    }

    /**
     * @test
     */
    public function it_should_return_false_on_is_view()
    {
        $table = new Table("user_status");
        $this->assertFalse($table->isView());
    }

    /**
     * @test
     */
    public function it_should_return_true_on_is_view()
    {
        $table = new Table("user_status");
        $table->setView(true);
        $this->assertTrue($table->isView());
    }
}
