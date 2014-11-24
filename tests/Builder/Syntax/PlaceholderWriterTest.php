<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:46 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\Syntax\PlaceholderWriter;

/**
 * Class PlaceholderWriterTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class PlaceholderWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PlaceholderWriter
     */
    private $writer;

    /**
     *
     */
    protected function setUp()
    {
        $this->writer = new PlaceholderWriter();
    }

    /**
     * @test
     */
    public function it_should_add_value_and_return_placeholder()
    {
        $result = $this->writer->add(1);
        $this->assertEquals(':v1', $result);
    }

    /**
     * @test
     */
    public function it_should_add_value_and_get_returns_array_holding_placeholder_data()
    {
        $this->writer->add(1);
        $this->assertEquals(array(':v1' => 1), $this->writer->get());
    }

    /**
     * @test
     */
    public function it_should_translate_php_null_to_sql_null_value()
    {
        $this->writer->add('');
        $this->writer->add(null);

        $this->assertEquals(array(':v1' => 'NULL', ':v2' => 'NULL'), $this->writer->get());
    }

    /**
     * @test
     */
    public function it_should_translate_php_bool_to_sql_bool_value()
    {
        $this->writer->add(true);
        $this->writer->add(false);

        $this->assertEquals(array(':v1' => 1, ':v2' => 0), $this->writer->get());
    }
}
