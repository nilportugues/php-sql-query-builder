<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/13/14
 * Time: 12:46 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class WhereWriterTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class WhereWriterTest extends \PHPUnit_Framework_TestCase
{
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
    }

    /**
     * @test
     */
    public function it_should_allow_where_conditions()
    {
        $this->query
            ->setTable('user')
            ->where()
            ->equals('user_id', 1)
            ->like('name', '%N%');

        $expected = 'SELECT user.* FROM user WHERE (user.user_id = :v1) AND (user.name LIKE :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => '%N%');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_allow_where_or_conditions()
    {

        $this->query
            ->setTable('user')
            ->where('OR')
            ->equals('user_id', 1)
            ->like('name', '%N%');

        $this->assertSame('OR', $this->query->getWhereOperator());

        $expected = 'SELECT user.* FROM user WHERE (user.user_id = :v1) OR (user.name LIKE :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => '%N%');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_not_be_equal_to()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->notEquals($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id <> :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_be_greater_than()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->greaterThan($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id > :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_be_greater_than_or_equal()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->greaterThanOrEqual($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id >= :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_be_less_than()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->lessThan($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id < :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_be_less_than_or_equal()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->lessThanOrEqual($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id <= :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_be_like()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->like($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id LIKE :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_be_not_like()
    {
        $column = 'user_id';
        $value  = 1;

        $this->query
            ->setTable('user')
            ->where()
            ->notLike($column, $value);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id NOT LIKE :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_accumulate_in_conditions()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->in($column, array(1, 2, 3));

        $expected = 'SELECT user.* FROM user WHERE (user.user_id IN (:v1, :v2, :v3))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 2, ':v3' => 3);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_accumulate_not_in_conditions()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->notIn($column, array(1, 2, 3));

        $expected = 'SELECT user.* FROM user WHERE (user.user_id NOT IN (:v1, :v2, :v3))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 2, ':v3' => 3);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_write_between_conditions()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->between($column, 1, 2);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id BETWEEN :v1 AND :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 2);
        $this->assertEquals($expected, $this->writer->getValues());

    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_set_null_value_condition()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->isNull($column);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id IS NULL)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array();
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_set_is_not_null_value_condition()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->isNotNull($column);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id IS NOT NULL)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array();
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_set_bit_clause_value_condition()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->addBitClause($column, 1);

        $expected = 'SELECT user.* FROM user WHERE (ISNULL(user.user_id, 0) = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_let_where_statement_subconditions()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->equals($column, 1)
            ->equals($column, 2)
            ->subWhere("OR")
            ->lessThan($column, 10)
            ->greaterThan($column, 100);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id = :v1) AND (user.user_id = :v2) '.
            'AND ((user.user_id < :v3) OR (user.user_id > :v4))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 2, ':v3' => 10, ':v4' => 100);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_allow_select_where_but_not_write_condition()
    {
        $table1 = new Select('Table1');
        $table1
            ->where();

        $expected = 'SELECT Table1.* FROM Table1';
        $this->assertSame($expected, $this->writer->write($table1));
    }

    /**
     * @test
     */
    public function it_should_allow_having_conditions()
    {
        $this->query
            ->setTable('user')
            ->having()
            ->greaterThan('user_id', 1)
            ->like('name', '%N%');

        $expected = 'SELECT user.* FROM user HAVING (user.user_id > :v1) AND (user.name LIKE :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => '%N%');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_use_select_statements_in_where()
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
            ->setColumns(array('user_id', 'role_id'))
            ->where()
            ->equals('role_id', $selectRole);

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE '.
            '(user.role_id = (SELECT role.role_name FROM role WHERE (role.role_id = :v1) LIMIT :v2, :v3))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 3, ':v2' => 1, ':v3' => 0);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_select_with_full_match_search_using_match_in_natural_mode()
    {
        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'role_id'))
            ->where()
            ->match(array('username', 'email'), array('Nil'));

        $expected = 'SELECT user.user_id, user.role_id FROM user '.
            'WHERE (MATCH(user.username, user.email) AGAINST(:v1))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 'Nil');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_select_with_full_match_search_using_match_in_boolean_mode()
    {
        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'role_id'))
            ->where()
            ->matchBoolean(array('username', 'email'), array('Nil'));

        $expected = 'SELECT user.user_id, user.role_id FROM user '.
            'WHERE (MATCH(user.username, user.email) AGAINST(:v1 IN BOOLEAN MODE))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 'Nil');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_select_with_full_match_search_using_match_in_query_expansion_mode()
    {
        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'role_id'))
            ->where()
            ->matchWithQueryExpansion(array('username', 'email'), array('Nil'));

        $expected = 'SELECT user.user_id, user.role_id FROM user '.
            'WHERE (MATCH(user.username, user.email) AGAINST(:v1 WITH QUERY EXPANSION))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 'Nil');
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
