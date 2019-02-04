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
    public function itShouldWriteUnion()
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
    public function itShouldWriteUnionFromGenericBuilder()
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

        $union = (new Union())
            ->add($select1)
            ->add($select2);

        $expectedSql = <<<SQL
SELECT table1.* FROM table1 WHERE (table1.column BETWEEN :v1 AND :v2)
UNION
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
