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
    public function itShouldWriteIntersect()
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

    /**
     * @test
     */
    public function itShouldNotResetPlaceholders()
    {
        $select1 = (new Select('table1'))
            ->where()
            ->between('column', 1, 2)
            ->end();

        $select2 = (new Select('table2'))
            ->where()
            ->between('column', 3, 4)
            ->end();

        $union = (new Intersect())
            ->add($select1)
            ->add($select2);

        $expectedSql = <<<SQL
SELECT table1.* FROM table1 WHERE (table1.column BETWEEN :v1 AND :v2)
INTERSECT
SELECT table2.* FROM table2 WHERE (table2.column BETWEEN :v3 AND :v4)
SQL;

        $expectedParams = [
            ':v1' => 1,
            ':v2' => 2,
            ':v3' => 3,
            ':v4' => 4,
        ];

        $this->assertEquals($expectedSql, $this->writer->write($union));
        $this->assertEquals($expectedParams, $this->writer->getValues());
    }
}
