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
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\UnionAllWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class UnionAllWriterTest.
 */
class UnionAllWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnionAllWriter
     */
    private $unionAllWriter;

    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     *
     */
    public function setUp()
    {
        $this->unionAllWriter = new UnionAllWriter(new GenericBuilder());
        $this->writer = new GenericBuilder();
    }

    public function tearDown()
    {
        $this->unionAllWriter = null;
        $this->writer = null;
    }

    /**
     * @test
     */
    public function itShouldWriteIntersects()
    {
        $union = new UnionAll();

        $union->add(new Select('user'));
        $union->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
UNION ALL
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->unionAllWriter->write($union));
    }

    /**
     * @test
     */
    public function itShouldWriteUnionAllFromGenericBuilder()
    {
        $unionAll = $this->writer->unionAll();

        $unionAll->add(new Select('user'));
        $unionAll->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
UNION ALL
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->writer->write($unionAll));
    }
}
