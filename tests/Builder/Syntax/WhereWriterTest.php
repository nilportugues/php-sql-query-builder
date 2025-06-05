<?php

declare(strict_types=1);

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
use PHPUnit\Framework\TestCase;

/**
 * Class WhereWriterTest.
 */
class WhereWriterTest extends TestCase
{
    private GenericBuilder $writer;
    private Select $query;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->query = new Select();
        $this->query->setBuilder($this->writer); // Where clauses generate placeholders
    }

    /**
     * @test
     */
    public function itShouldAllowWhereConditions(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->equals('user_id', 1)
            ->like('name', '%N%');

        $expected = 'SELECT user.* FROM user WHERE (user.user_id = :v1) AND (user.name LIKE :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => '%N%'];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldAllowWhereOrConditions(): void
    {
        $this->query
            ->setTable('user')
            ->where('OR')
            ->equals('user_id', 1)
            ->like('name', '%N%');

        $this->assertSame('OR', $this->query->getWhereOperator());

        $expected = 'SELECT user.* FROM user WHERE (user.user_id = :v1) OR (user.name LIKE :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => '%N%'];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementNotBeEqualTo(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->notEquals('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id <> :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementBeGreaterThan(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->greaterThan('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id > :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementBeGreaterThanOrEqual(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->greaterThanOrEqual('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id >= :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementBeLessThan(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->lessThan('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id < :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementBeLessThanOrEqual(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->lessThanOrEqual('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id <= :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementBeLike(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->like('user_id', 1); // Value 1 will be treated as string '1' by PDO or similar

        $expected = 'SELECT user.* FROM user WHERE (user.user_id LIKE :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementBeNotLike(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->notLike('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id NOT LIKE :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementAccumulateInConditions(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->in('user_id', [1, 2, 3]);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id IN (:v1, :v2, :v3))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => 2, ':v3' => 3];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementAccumulateNotInConditions(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->notIn('user_id', [1, 2, 3]);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id NOT IN (:v1, :v2, :v3))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => 2, ':v3' => 3];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementWriteBetweenConditions(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->between('user_id', 1, 2);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id BETWEEN :v1 AND :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementWriteNotBetweenConditions(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->notBetween('user_id', 1, 2);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id NOT BETWEEN :v1 AND :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementSetNullValueCondition(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->isNull('user_id');

        $expected = 'SELECT user.* FROM user WHERE (user.user_id IS NULL)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementSetIsNotNullValueCondition(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->isNotNull('user_id');

        $expected = 'SELECT user.* FROM user WHERE (user.user_id IS NOT NULL)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementSetBitClauseValueCondition(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->addBitClause('user_id', 1);

        $expected = 'SELECT user.* FROM user WHERE (ISNULL(user.user_id, 0) = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToLetWhereStatementSubconditions(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->equals('user_id', 1)
            ->equals('user_id', 2)
            ->subWhere('OR') // This creates a new Where object, its table context needs to be set
            ->lessThan('user_id', 10)
            ->greaterThan('user_id', 100);

        $expected = 'SELECT user.* FROM user WHERE (user.user_id = :v1) AND (user.user_id = :v2) AND ((user.user_id < :v3) OR (user.user_id > :v4))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => 2, ':v3' => 10, ':v4' => 100];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldAllowSelectWhereButNotWriteCondition(): void
    {
        $table1 = new Select('Table1');
        $table1->setBuilder($this->writer);
        $table1->where(); // Calling where() creates a Where object but adds no conditions yet

        $expected = 'SELECT Table1.* FROM Table1';
        $this->assertSame($expected, $this->writer->write($table1));
    }

    /**
     * @test
     */
    public function itShouldAllowHavingConditions(): void
    {
        $this->query
            ->setTable('user')
            ->having()
            ->greaterThan('user_id', 1)
            ->like('name', '%N%');

        $expected = 'SELECT user.* FROM user HAVING (user.user_id > :v1) AND (user.name LIKE :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 1, ':v2' => '%N%'];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToUseSelectStatementsInWhere(): void
    {
        $selectRole = new Select();
        $selectRole->setBuilder($this->writer);
        $selectRole
            ->setTable('role')
            ->setColumns(['role_name'])
            ->limit(1, 0) // limit(count, offset)
            ->where()
            ->equals('role_id', 3);

        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'role_id'])
            ->where()
            ->equals('role_id', $selectRole);

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (user.role_id = (SELECT role.role_name FROM role WHERE (role.role_id = :v1) LIMIT :v2, :v3))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 3, ':v2' => 1, ':v3' => 0];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSelectWithFullMatchSearchUsingMatchInNaturalMode(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'role_id'])
            ->where()
            ->match(['username', 'email'], ['Nil']);

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (MATCH(user.username, user.email) AGAINST(:v1))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 'Nil'];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSelectWithFullMatchSearchUsingMatchInBooleanMode(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'role_id'])
            ->where()
            ->matchBoolean(['username', 'email'], ['Nil']);

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (MATCH(user.username, user.email) AGAINST(:v1 IN BOOLEAN MODE))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 'Nil'];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSelectWithFullMatchSearchUsingMatchInQueryExpansionMode(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'role_id'])
            ->where()
            ->matchWithQueryExpansion(['username', 'email'], ['Nil']);

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (MATCH(user.username, user.email) AGAINST(:v1 WITH QUERY EXPANSION))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 'Nil'];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoWhereExists(): void
    {
        $select = new Select('banned_user');
        $select->setBuilder($this->writer);
        $select->where()->equals('user_id', 1);

        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'role_id'])
            ->where()
            ->exists($select)
            ->equals('user', 'Nil');

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (user.user = :v1) AND EXISTS (SELECT banned_user.* FROM banned_user WHERE (banned_user.user_id = :v2))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 'Nil', ':v2' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoWhereNotExists(): void
    {
        $select = new Select('banned_user');
        $select->setBuilder($this->writer);
        $select->where()->equals('user_id', 1);

        $this->query
            ->setTable('user')
            ->setColumns(['user_id', 'role_id'])
            ->where()
            ->notExists($select)
            ->equals('user', 'Nil');

        $expected = 'SELECT user.user_id, user.role_id FROM user WHERE (user.user = :v1) AND NOT EXISTS (SELECT banned_user.* FROM banned_user WHERE (banned_user.user_id = :v2))';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => 'Nil', ':v2' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldAllowWhereConditionAsLiteral(): void
    {
        $this->query
            ->setTable('user')
            ->where()
            ->asLiteral('(username is not null and status=:status)')
            ->notEquals('name', '%N%');

        $expected = 'SELECT user.* FROM user WHERE (username is not null and status=:status) AND (user.name <> :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expectedValues = [':v1' => '%N%']; // :status is not a placeholder generated by the library here
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }
}
