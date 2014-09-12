<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:45 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Builder\Syntax\ColumnWriter;
use NilPortugues\SqlQueryBuilder\Builder\Syntax\PlaceholderWriter;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;
use NilPortugues\SqlQueryBuilder\Syntax\Column;

/**
 * Class ColumnWriterTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class ColumnWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnWriter
     */
    private $columnWriter;

    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var Select
     */
    private $query;

    /**
     *
     */
    protected function setUp()
    {
        $this->writer = new GenericBuilder();
        $this->query  = new Select();
        $this->columnWriter  = new ColumnWriter(new GenericBuilder(), new PlaceholderWriter());
    }

    /**
     * @test
     */
    public function it_should_write_column()
    {
        $column = new Column('user_id', 'user');

        $result = $this->columnWriter->writeColumn($column);

        $this->assertSame('user.user_id', $result);
    }

    /**
     * @test
     */
    public function it_should_write_value_as_columns()
    {
        $select = new Select('user');
        $select->setValueAsColumn('1', 'user_id');

        $result = $this->columnWriter->writeValueAsColumns($select);

        $this->assertInstanceOf('NilPortugues\SqlQueryBuilder\Syntax\Column', $result[0]);
    }

    /**
     * @test
     */
    public function it_should_write_func_as_columns()
    {
        $select = new Select('user');
        $select->setFunctionAsColumn('MAX', ['user_id'], 'max_value');

        $result = $this->columnWriter->writeFuncAsColumns($select);

        $this->assertInstanceOf('NilPortugues\SqlQueryBuilder\Syntax\Column', $result[0]);
    }

    /**
     * @test
     */
    public function it_should_write_column_with_alias()
    {
        $column = new Column('user_id', 'user', 'userId');

        $result = $this->columnWriter->writeColumnWithAlias($column);

        $this->assertSame('user.user_id AS userId', $result);
    }

    /**
     * @test
     */
    public function it_should_be_able_to_write_column_as_a_select_statement()
    {

        $selectRole = new Select();
        $selectRole
            ->setTable('role')
            ->setColumns(array('role_name'))
            ->limit(1)
            ->where()
            ->equals('role_id', 3);

        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'username'))
            ->setSelectAsColumn(array('user_role' => $selectRole))
            ->setSelectAsColumn(array($selectRole))
            ->where()
            ->equals('user_id', 4);

        $expected = 'SELECT user.user_id, user.username, '.
            '(SELECT role.role_name FROM role WHERE (role.role_id = :v1) LIMIT :v2, :v3) AS user_role, '.
            '(SELECT role.role_name FROM role WHERE (role.role_id = :v4) LIMIT :v5, :v6) AS role '.
            'FROM user WHERE (user.user_id = :v7)';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 3, ':v2' => 1, ':v3' => 0, ':v4' => 3, ':v5' => 1, ':v6' => 0, ':v7' => 4);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_write_column_as_a_value_statement()
    {
        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'username'))
            ->setValueAsColumn('10', 'priority')
            ->where()
            ->equals('user_id', 1);

        $expected = 'SELECT user.user_id, user.username, :v1 AS priority FROM user WHERE (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 10, ':v2' => 1);
        $this->assertEquals($expected, $this->writer->getValues());

    }

    /**
     * @test
     */
    public function it_should_be_able_to_write_column_as_a_func_with_brackets_statement()
    {
        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'username'))
            ->setFunctionAsColumn('MAX', array('user_id'), 'max_id')
            ->where()
            ->equals('user_id', 1);

        $expected = 'SELECT user.user_id, user.username, MAX(user_id) AS max_id FROM user WHERE (user.user_id = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_write_column_as_a_func_without_brackets_statement()
    {
        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'username'))
            ->setFunctionAsColumn('CURRENT_TIMESTAMP', array(), 'server_time')
            ->where()
            ->equals('user_id', 1);

        $expected = 'SELECT user.user_id, user.username, CURRENT_TIMESTAMP AS server_time FROM user WHERE (user.user_id = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
