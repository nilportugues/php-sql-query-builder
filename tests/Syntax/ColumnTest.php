<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/2/14
 * Time: 11:54 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Syntax;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;

/**
 * Class ColumnTest.
 */
class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $tableClass = '\NilPortugues\Sql\QueryBuilder\Syntax\Table';

    /**
     * @var string
     */
    protected $queryException = '\NilPortugues\Sql\QueryBuilder\Manipulation\QueryException';

    /**
     * @test
     */
    public function itShouldReturnPartName()
    {
        $column = new Column('id', 'user');

        $this->assertSame('COLUMN', $column->partName());
    }

    /**
     * @test
     */
    public function itShouldConstruct()
    {
        $column = new Column('id', 'user');

        $this->assertEquals('id', $column->getName());
        $this->assertInstanceOf($this->tableClass, $column->getTable());
        $this->assertEquals('user', $column->getTable()->getName());
    }

    /**
     * @test
     */
    public function itShouldSetColumnName()
    {
        $column = new Column('id', 'user');

        $column->setName('user_id');
        $this->assertEquals('user_id', $column->getName());
    }

    /**
     * @test
     */
    public function itShouldSetTableName()
    {
        $tableName = 'user';

        $column = new Column('id', $tableName);
        $column->setTable(new Table($tableName));

        $this->assertInstanceOf($this->tableClass, $column->getTable());
        $this->assertEquals($tableName, $column->getTable()->getName());
    }

    /**
     * @test
     */
    public function itShouldSetAliasName()
    {
        $column = new Column('user_id', 'user', 'userId');
        $this->assertEquals('userId', $column->getAlias());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionIfAliasOnAllSelection()
    {
        $this->setExpectedException($this->queryException);

        new Column('*', 'user', 'userId');
    }
}
