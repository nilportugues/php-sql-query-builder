<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:47 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;

/**
 * Class UpdateWriterTest.
 */
class UpdateWriterTest extends \PHPUnit_Framework_TestCase
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
    private $exceptionClass = '\NilPortugues\Sql\QueryBuilder\Manipulation\QueryException';

    /**
     *
     */
    protected function setUp()
    {
        $this->writer = new GenericBuilder();
        $this->query = new Update();

        $this->valueArray = array(
            'user_id' => 1,
            'name' => 'Nil',
            'contact' => 'contact@nilportugues.com',
        );
    }

    /**
     * @test
     */
    public function itShouldThrowQueryException()
    {
        $this->setExpectedException($this->exceptionClass);

        $this->query->setTable('user');
        $this->writer->write($this->query);
    }

    /**
     * @test
     */
    public function itShouldWriteUpdateQuery()
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
    public function itShouldBeAbleToWriteCommentInQuery()
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->setComment('This is a comment');

        $expected = <<<SQL
-- This is a comment
UPDATE user SET  user.user_id = :v1, user.name = :v2, user.contact = :v3
SQL;
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com');
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldWriteUpdateQueryWithWhereConstrain()
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->where()
            ->equals('user_id', 1);

        $expected = 'UPDATE user SET  user.user_id = :v1, user.name = :v2, user.contact = :v3 WHERE (user.user_id = :v4)';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldWriteUpdateQueryWithWhereConstrainAndLimit1()
    {
        $this->query
            ->setTable('user')
            ->setValues($this->valueArray)
            ->where()
            ->equals('user_id', 1);

        $this->query->limit(1);

        $expected = 'UPDATE user SET  user.user_id = :v1, user.name = :v2, user.contact = :v3 WHERE (user.user_id = :v4) LIMIT :v5';
        $this->assertSame($expected, $this->writer->write($this->query));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1, ':v5' => 1);
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
