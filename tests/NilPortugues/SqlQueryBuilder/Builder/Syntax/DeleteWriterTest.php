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
use NilPortugues\SqlQueryBuilder\Manipulation\Delete;

/**
 * Class DeleteWriterTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class DeleteWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var Delete
     */
    private $query;

    /**
     *
     */
    protected function setUp()
    {
        $this->writer = new GenericBuilder();
        $this->query  = new Delete();
    }

    /**
     * @test
     */
    public function it_should_write_delete_all_table_contents_query()
    {
        $this->query->setTable('user');

        $expected = 'DELETE FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function it_should_write_delete_row_limit_1()
    {
        $this->query
            ->setTable('user')
            ->limit(1);

        $expected = "DELETE FROM user LIMIT :v1";
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_write_delete_row_with_where_condition_and_limit_1()
    {
        $this->query->setTable('user');

        $conditions = $this->query->where();
        $conditions
            ->equals('user_id', 10)
            ->equals('user_id', 20)
            ->equals('user_id', 30);

        $this->query->limit(1);

        $expected = <<<SQL
DELETE FROM user WHERE (user.user_id = :v1) AND (user.user_id = :v2) AND (user.user_id = :v3) LIMIT :v4
SQL;
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 10, ':v2' => 20, ':v3' => 30, ':v4' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
