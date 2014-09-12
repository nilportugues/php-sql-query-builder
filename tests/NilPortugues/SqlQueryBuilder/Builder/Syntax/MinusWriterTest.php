<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:34 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Builder\Syntax\MinusWriter;
use NilPortugues\SqlQueryBuilder\Manipulation\Minus;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class MinusWriterTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class MinusWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MinusWriter
     */
    private $writer;

    /**
     *
     */
    public function setUp()
    {
        $this->writer = new MinusWriter(new GenericBuilder());
    }

    public function tearDown()
    {
        $this->writer = null;
    }

    /**
     * @test
     */
    public function it_should_write_minus()
    {
        $minus = new Minus();

        $minus->first(new Select('user'));
        $minus->second(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
MINUS
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->writer->writeMinus($minus));
    }
}
