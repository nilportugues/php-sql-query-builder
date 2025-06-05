<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/2/14
 * Time: 11:54 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Syntax;

use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use PHPUnit\Framework\TestCase;

/**
 * Class ColumnTest.
 */
class ColumnTest extends TestCase
{
    protected string $tableClass = Table::class;
    protected string $queryExceptionClass = QueryException::class; // Renamed for clarity

    /**
     * @test
     */
    public function itShouldReturnPartName(): void
    {
        $column = new Column('id', 'user');
        $this->assertSame('COLUMN', $column->partName());
    }

    /**
     * @test
     */
    public function itShouldConstruct(): void
    {
        $column = new Column('id', 'user');
        $this->assertEquals('id', $column->getName());
        $table = $column->getTable();
        $this->assertInstanceOf($this->tableClass, $table);
        $this->assertNotNull($table); // To satisfy static analysis that $table is not null before getName
        $this->assertEquals('user', $table->getName());
    }

    /**
     * @test
     */
    public function itShouldSetColumnName(): void
    {
        $column = new Column('id', 'user');
        $column->setName('user_id');
        $this->assertEquals('user_id', $column->getName());
    }

    /**
     * @test
     */
    public function itShouldSetTableName(): void
    {
        $tableName = 'user';
        $column = new Column('id', $tableName);
        $column->setTable($tableName); // Column::setTable expects ?string

        $table = $column->getTable();
        $this->assertInstanceOf($this->tableClass, $table);
        $this->assertNotNull($table);
        $this->assertEquals($tableName, $table->getName());
    }

    /**
     * @test
     */
    public function itShouldSetAliasName(): void
    {
        $column = new Column('user_id', 'user', 'userId');
        $this->assertEquals('userId', $column->getAlias());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionIfAliasOnAllSelection(): void
    {
        $this->expectException($this->queryExceptionClass);
        new Column('*', 'user', 'userId');
    }
}
