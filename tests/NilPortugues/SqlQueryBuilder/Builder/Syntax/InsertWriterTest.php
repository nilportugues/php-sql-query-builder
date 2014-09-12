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
use NilPortugues\SqlQueryBuilder\Manipulation\Insert;

/**
 * Class InsertWriterTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class InsertWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var Insert
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
        $this->query  = new Insert();
    }

    /**
     * @test
     */
    public function it_should_throw_query_exception_because_no_columns_were_defined()
    {
        $this->setExpectedException($this->exceptionClass, 'No columns were defined for the current schema.');

        $this->query->setTable('user');
        $this->writer->write($this->query);
    }

    /**
     * @test
     */
    public function it_should_write_insert_query()
    {
        $valueArray = array(
            'user_id' => 1,
            'name'    => 'Nil',
            'contact' => 'contact@nilportugues.com',
        );

        $this->query
            ->setTable('user')
            ->setValues($valueArray);

        $expected = 'INSERT INTO user (user.user_id, user.name, user.contact) VALUES (:v1, :v2, :v3)';

        $this->assertSame($expected, $this->writer->write($this->query));
        $this->assertEquals(array_values($valueArray), array_values($this->query->getValues()));

        $expected = array(':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com');
        $this->assertEquals($expected, $this->writer->getValues());
    }
}
