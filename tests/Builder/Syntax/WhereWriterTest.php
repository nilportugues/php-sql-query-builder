<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/13/14
 * Time: 12:46 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class WhereWriterTest.
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
        $this->query = new Select();
    }

    /**
     * @test
     */
    public function itShouldAllowWhereConditions()
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
    public function itShouldAllowWhereOrConditions()
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
    public function itShouldBeAbleToLetWhereStatementNotBeEqualTo()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementBeGreaterThan()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementBeGreaterThanOrEqual()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementBeLessThan()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementBeLessThanOrEqual()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementBeLike()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementBeNotLike()
    {
        $column = 'user_id';
        $value = 1;

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
    public function itShouldBeAbleToLetWhereStatementAccumulateInConditions()
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
    public function itShouldBeAbleToLetWhereStatementAccumulateNotInConditions()
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
    public function itShouldBeAbleToLetWhereStatementWriteBetweenConditions()
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
    public function itShouldBeAbleToLetWhereStatementWriteNotBetweenConditions()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->notBetween($column, 1, 2);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id NOT BETWEEN :v1 AND :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 2);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementSetNullValueCondition()
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
    public function itShouldBeAbleToLetWhereStatementSetIsNotNullValueCondition()
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
    public function itShouldBeAbleToLetWhereStatementSetBitClauseValueCondition()
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
    public function itShouldBeAbleToLetWhereStatementSubconditions()
    {
        $column = 'user_id';

        $this->query
            ->setTable('user')
            ->where()
            ->equals($column, 1)
            ->equals($column, 2)
            ->subWhere('OR')
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
    public function itShouldAllowSelectWhereButNotWriteCondition()
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
    public function itShouldAllowHavingConditions()
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
    public function itShouldBeAbleToUseSelectStatementsInWhere()
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
    public function itShouldBeAbleToSelectWithFullMatchSearchUsingMatchInNaturalMode()
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
    public function itShouldBeAbleToSelectWithFullMatchSearchUsingMatchInBooleanMode()
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
    public function itShouldBeAbleToSelectWithFullMatchSearchUsingMatchInQueryExpansionMode()
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

    /**
     * @test
     */
    public function itShouldBeAbleToDoWhereExists()
    {
        $select = new Select('banned_user');
        $select->where()->equals('user_id', 1);

        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'role_id'))
            ->where()
            ->exists($select)
            ->equals('user', 'Nil');

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (user.user = :v1) AND '.
            'EXISTS (SELECT banned_user.* FROM banned_user WHERE (banned_user.user_id = :v2))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 'Nil', ':v2' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoWhereNotExists()
    {
        $select = new Select('banned_user');
        $select->where()->equals('user_id', 1);

        $this->query
            ->setTable('user')
            ->setColumns(array('user_id', 'role_id'))
            ->where()
            ->notExists($select)
            ->equals('user', 'Nil');

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (user.user = :v1) AND '.
            'NOT EXISTS (SELECT banned_user.* FROM banned_user WHERE (banned_user.user_id = :v2))';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 'Nil', ':v2' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldAllowWhereConditionAsLiteral()
    {
        $this->query
            ->setTable('user')
            ->where()
            ->asLiteral('(username is not null and status=:status)')
            ->notEquals('name', '%N%');

        $expected = 'SELECT user.* FROM user WHERE (username is not null and status=:status) AND (user.name <> :v1)';

        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => '%N%');
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
