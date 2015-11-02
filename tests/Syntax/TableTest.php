<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/2/14
 * Time: 11:34 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Syntax;

use NilPortugues\Sql\QueryBuilder\Syntax\Table;

/**
 * Class TableTest.
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testConstruct()
    {
        $table = new Table('user');
        $this->assertEquals('user', $table->getName());
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfTableNameHasNoAlias()
    {
        $table = new Table('user');
        $this->assertNull($table->getAlias());
    }

    /**
     * @test
     */
    public function itShouldReturnAliasIfTableNameAliasHasBeenSet()
    {
        $table = new Table('user');
        $table->setAlias('u');
        $this->assertEquals('u', $table->getAlias());
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfSchemaNotSet()
    {
        $table = new Table('user');
        $this->assertNull($table->getSchema());
    }

    /**
     * @test
     */
    public function itShouldReturnSchemaIfSchemaHasValue()
    {
        $table = new Table('user', 'website');
        $this->assertEquals('website', $table->getSchema());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCompleteName()
    {
        $table = new Table('user');

        $table->setAlias('p');
        $table->setSchema('website');

        $this->assertEquals('website.user AS p', $table->getCompleteName());
    }

    /**
     * @test
     */
    public function itShouldReturnFalseOnIsView()
    {
        $table = new Table('user_status');
        $this->assertFalse($table->isView());
    }

    /**
     * @test
     */
    public function itShouldReturnTrueOnIsView()
    {
        $table = new Table('user_status');
        $table->setView(true);
        $this->assertTrue($table->isView());
    }
}
