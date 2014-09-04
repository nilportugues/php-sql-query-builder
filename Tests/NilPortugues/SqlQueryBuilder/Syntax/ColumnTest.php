<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/2/14
 * Time: 11:54 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Syntax;

use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\Table;

/**
 * Class ColumnTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Syntax
 */
class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $tableClass = '\NilPortugues\SqlQueryBuilder\Syntax\Table';

    /**
     * @var string
     */
    protected $queryException = '\NilPortugues\SqlQueryBuilder\Manipulation\QueryException';

    /**
     * @test
     */
    public function it_should_return_part_name()
    {
        $column = new Column("id", "user");

        $this->assertSame('COLUMN', $column->partName());
    }

    /**
     * @test
     */
    public function it_should_construct()
    {
        $column = new Column("id", "user");

        $this->assertEquals("id", $column->getName());
        $this->assertInstanceOf($this->tableClass, $column->getTable());
        $this->assertEquals("user", $column->getTable()->getName());
    }

    /**
     * @test
     */
    public function it_should_set_column_name()
    {
        $column = new Column("id", "user");

        $column->setName("user_id");
        $this->assertEquals("user_id", $column->getName());
    }

    /**
     * @test
     */
    public function it_should_set_table_name()
    {
        $tableName = "user";

        $column = new Column("id", $tableName);
        $column->setTable(new Table($tableName));

        $this->assertInstanceOf($this->tableClass, $column->getTable());
        $this->assertEquals($tableName, $column->getTable()->getName());
    }

    /**
     * @test
     */
    public function it_should_set_alias_name()
    {
        $column = new Column("user_id", "user", "userId");
        $this->assertEquals("userId", $column->getAlias());
    }

    /**
     * @test
     */
    public function it_should_throw_exception_if_alias_on_all_selection()
    {
        $this->setExpectedException($this->queryException);

        $column = new Column("*", "user", "userId");
        $column->getAlias();
    }
}
