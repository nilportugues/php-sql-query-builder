<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/16/14
 * Time: 8:56 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Builder;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

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
    public function it_should_create_select_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Select';
        $this->assertInstanceOf($className, $this->writer->select());
    }

    /**
     * @test
     */
    public function it_should_create_insert_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Insert';
        $this->assertInstanceOf($className, $this->writer->insert());
    }

    /**
     * @test
     */
    public function it_should_create_update_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Update';
        $this->assertInstanceOf($className, $this->writer->update());
    }

    /**
     * @test
     */
    public function it_should_create_delete_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Delete';
        $this->assertInstanceOf($className, $this->writer->delete());
    }

    /**
     * @test
     */
    public function it_should_create_intersect_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Intersect';
        $this->assertInstanceOf($className, $this->writer->intersect());
    }

    /**
     * @test
     */
    public function it_should_create_minus_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Minus';
        $this->assertInstanceOf($className, $this->writer->minus(new Select('table1'), new Select('table2')));
    }

    /**
     * @test
     */
    public function it_should_create_union_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\Union';
        $this->assertInstanceOf($className, $this->writer->union());
    }

    /**
     * @test
     */
    public function it_should_create_union_all_object()
    {
        $className = '\NilPortugues\SqlQueryBuilder\Manipulation\UnionAll';
        $this->assertInstanceOf($className, $this->writer->unionAll());
    }

    /**
     * @test
     */
    public function it_should_ouput_human_readable_query()
    {
        $selectRole =  $this->writer->select();
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
    ) AS 'user_role',
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
    ) AS 'role'
FROM
    user
WHERE
    (user.user_id = :v7)

QUERY;

        $this->assertSame($expected, $this->writer->writeFormatted($select));
    }
}
