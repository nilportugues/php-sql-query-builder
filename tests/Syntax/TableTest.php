<?php

declare(strict_types=1);
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
use PHPUnit\Framework\TestCase;

/**
 * Class TableTest.
 */
class TableTest extends TestCase
{
    /**
     * @test
     */
    public function testConstruct(): void
    {
        $table = new Table('user');
        $this->assertEquals('user', $table->getName());
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfTableNameHasNoAlias(): void
    {
        $table = new Table('user');
        $this->assertNull($table->getAlias());
    }

    /**
     * @test
     */
    public function itShouldReturnAliasIfTableNameAliasHasBeenSet(): void
    {
        $table = new Table('user');
        $table->setAlias('u');
        $this->assertEquals('u', $table->getAlias());
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfSchemaNotSet(): void
    {
        $table = new Table('user');
        $this->assertNull($table->getSchema());
    }

    /**
     * @test
     */
    public function itShouldReturnSchemaIfSchemaHasValue(): void
    {
        $table = new Table('user', 'website');
        $this->assertEquals('website', $table->getSchema());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCompleteName(): void
    {
        $table = new Table('user');

        $table->setAlias('p');
        $table->setSchema('website');

        $this->assertEquals('website.user AS p', $table->getCompleteName());
    }

    /**
     * @test
     */
    public function itShouldReturnFalseOnIsView(): void
    {
        $table = new Table('user_status');
        $this->assertFalse($table->isView());
    }

    /**
     * @test
     */
    public function itShouldReturnTrueOnIsView(): void
    {
        $table = new Table('user_status');
        $table->setView(true);
        $this->assertTrue($table->isView());
    }
}
