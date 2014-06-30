<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:37 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Update;

/**
 * Class UpdateTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
 */
class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $valueArray = array();

    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var Update
     */
    private $query;

    /**
     * @var string
     */
    private $exceptionClass = '\NilPortugues\SqlQueryBuilder\Manipulation\QueryException';

    /**
     *
     */
    protected function setUp()
    {
        $this->writer = new GenericBuilder();
        $this->query  = new Update();

        $this->valueArray = array(
            'user_id' => 1,
            'name'    => 'Nil',
            'contact' => 'contact@nilportugues.com',
        );
    }

    /**
     * @test
     */
    public function it_should_throw_query_exception()
    {
        $this->setExpectedException($this->exceptionClass);

        $this->query->setTable('user');
        $this->writer->write($this->query);
    }

    /**
     * @test
     */
    public function it_should_write_update_query()
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray);

        $expected = 'UPDATE user SET  user.user_id = :v1, user.name = :v2, user.contact = :v3';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_write_update_query_with_where_constrain()
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->where()
            ->equals('user_id', 1);

        $expected = 'UPDATE user SET  user.user_id = :v1, user.name = :v2, user.contact = :v3  WHERE (user.user_id = :v4)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function it_should_write_update_query_with_where_constrain_and_limit_1()
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->where()
            ->equals('user_id', 1);

        $this->query->limit(1);

        $expected = 'UPDATE user SET  user.user_id = :v1, user.name = :v2, user.contact = :v3  WHERE (user.user_id = :v4) LIMIT :v5';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1, ':v5' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
