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
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\IntersectWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\Intersect;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class IntersectWriterTest.
 */
class IntersectWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var IntersectWriter
     */
    private $intersectWriter;

    /**
     *
     */
    public function setUp()
    {
        $this->intersectWriter = new IntersectWriter(new GenericBuilder());
        $this->writer = new GenericBuilder();
    }

    public function tearDown()
    {
        $this->intersectWriter = null;
        $this->writer = null;
    }

    /**
     * @test
     */
    public function itShouldWriteIntersects()
    {
        $intersect = new Intersect();

        $intersect->add(new Select('user'));
        $intersect->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
INTERSECT
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->intersectWriter->write($intersect));
    }

    /**
     * @test
     */
    public function itShouldWriteIntersectFromGenericBuilder()
    {
        $intersect = $this->writer->intersect();

        $intersect->add(new Select('user'));
        $intersect->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
INTERSECT
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->writer->write($intersect));
    }
}
