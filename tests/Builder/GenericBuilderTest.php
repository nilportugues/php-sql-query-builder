<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/16/14
 * Time: 8:56 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

class GenericBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     *
     */
    public function setUp()
    {
        $this->writer = new GenericBuilder();
    }

    /**
     * @test
     */
    public function itShouldCreateSelectObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Select';
        $this->assertInstanceOf($className, $this->writer->select());
    }

    /**
     * @test
     */
    public function itShouldCreateInsertObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Insert';
        $this->assertInstanceOf($className, $this->writer->insert());
    }

    /**
     * @test
     */
    public function itShouldCreateUpdateObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Update';
        $this->assertInstanceOf($className, $this->writer->update());
    }

    /**
     * @test
     */
    public function itShouldCreateDeleteObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Delete';
        $this->assertInstanceOf($className, $this->writer->delete());
    }

    /**
     * @test
     */
    public function itShouldCreateIntersectObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Intersect';
        $this->assertInstanceOf($className, $this->writer->intersect());
    }

    /**
     * @test
     */
    public function itShouldCreateMinusObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Minus';
        $this->assertInstanceOf($className, $this->writer->minus(new Select('table1'), new Select('table2')));
    }

    /**
     * @test
     */
    public function itShouldCreateUnionObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\Union';
        $this->assertInstanceOf($className, $this->writer->union());
    }

    /**
     * @test
     */
    public function itShouldCreateUnionAllObject()
    {
        $className = '\NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll';
        $this->assertInstanceOf($className, $this->writer->unionAll());
    }

    /**
     * @test
     */
    public function itCanAcceptATableNameForSelectInsertUpdateDeleteQueries()
    {
        $table = 'user';
        $queries = [
            'select' => $this->writer->select($table),
            'insert' => $this->writer->insert($table),
            'update' => $this->writer->update($table),
            'delete' => $this->writer->delete($table),
        ];

        foreach ($queries as $type => $query) {
            $this->assertEquals($table, $query->getTable()->getName(), "Checking table in $type query");
        }
    }

    /**
     * @test
     */
    public function itCanAcceptATableAndColumnsForSelect()
    {
        $table = 'user';
        $columns = ['id', 'role'];
        $expected = <<<QUERY
SELECT
    user.id,
    user.role
FROM
    user

QUERY;

        $select = $this->writer->select($table, $columns);
        $this->assertSame($expected, $this->writer->writeFormatted($select));
    }

    /**
     * @test
     */
    public function itCanAcceptATableAndValuesForInsert()
    {
        $table = 'user';
        $values = ['id' => 1, 'role' => 'admin'];
        $expected = <<<QUERY
INSERT INTO user (user.id, user.role)
VALUES
    (:v1, :v2)

QUERY;

        $insert = $this->writer->insert($table, $values);
        $this->assertSame($expected, $this->writer->writeFormatted($insert));
    }

    /**
     * @test
     */
    public function itCanAcceptATableAndValuesForUpdate()
    {
        $table = 'user';
        $values = ['id' => 1, 'role' => 'super-admin'];
        $expected = <<<QUERY
UPDATE
    user
SET
    user.id = :v1,
    user.role = :v2

QUERY;

        $update = $this->writer->update($table, $values);
        $this->assertSame($expected, $this->writer->writeFormatted($update));
    }

    /**
     * @test
     */
    public function itShouldOutputHumanReadableQuery()
    {
        $selectRole = $this->writer->select();
        $selectRole
            ->setTable('role')
            ->setColumns(array('role_name'))
            ->limit(1)
            ->where()
            ->equals('role_id', 3);

        $select = $this->writer->select();
        $select->setTable('user')
            ->setColumns(array('user_id', 'username'))
            ->setSelectAsColumn(array('user_role' => $selectRole))
            ->setSelectAsColumn(array($selectRole))
            ->where()
            ->equals('user_id', 4);

        $expected = <<<QUERY
SELECT
    user.user_id,
    user.username,
    (
        SELECT
            role.role_name
        FROM
            role
        WHERE
            (role.role_id = :v1)
        LIMIT
            :v2,
            :v3
    ) AS "user_role",
    (
        SELECT
            role.role_name
        FROM
            role
        WHERE
            (role.role_id = :v4)
        LIMIT
            :v5,
            :v6
    ) AS "role"
FROM
    user
WHERE
    (user.user_id = :v7)

QUERY;

        $this->assertSame($expected, $this->writer->writeFormatted($select));
    }

    /**
     * @test
     */
    public function it_should_inject_the_builder()
    {
        $query = $this->writer->select();

        $this->assertSame($this->writer, $query->getBuilder());
    }

    /**
     * @test
     */
    public function itShouldWriteWhenGettingSql()
    {
        $query = $this->writer->select()
            ->setTable('user');

        $expected = $this->writer->write($query);

        $this->assertSame($expected, $query->getSql());
    }

    /**
     * @test
     */
    public function itShouldWriteFormattedWhenGettingFormattedSql()
    {
        $query = $this->writer->select()
            ->setTable('user');

        $formatted = true;
        $expected = $this->writer->writeFormatted($query);

        $this->assertSame($expected, $query->getSql($formatted));
    }
    /**
     * @test
     */
    public function itShouldWriteSqlWhenCastToString()
    {
        $query = $this->writer->select()
            ->setTable('user');

        $expected = $this->writer->write($query);

        $this->assertSame($expected, (string) $query);
    }
}
