<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:34 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\UnionWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\Union;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class UnionWriterTest.
 */
class UnionWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnionWriter
     */
    private $unionWriter;

    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     *
     */
    public function setUp()
    {
        $this->unionWriter = new UnionWriter(new GenericBuilder());
        $this->writer = new GenericBuilder();
    }

    public function tearDown()
    {
        $this->unionWriter = null;
        $this->writer = null;
    }

    /**
     * @test
     */
    public function itShouldWriteIntersects()
    {
        $union = new Union();

        $union->add(new Select('user'));
        $union->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
UNION
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->unionWriter->write($union));
    }

    /**
     * @test
     */
    public function itShouldWriteUnionAllFromGenericBuilder()
    {
        $unionAll = $this->writer->union();

        $unionAll->add(new Select('user'));
        $unionAll->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
UNION
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->writer->write($unionAll));
    }
}
