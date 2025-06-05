<?php

declare(strict_types=1);

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
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Union;
use PHPUnit\Framework\TestCase;

/**
 * Class UnionWriterTest.
 */
class UnionWriterTest extends TestCase
{
    private UnionWriter $unionWriter;
    private GenericBuilder $writer;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->unionWriter = new UnionWriter($this->writer);
    }

    protected function tearDown(): void
    {
        // Properties will be automatically garbage collected.
    }

    /**
     * @test
     */
    public function itShouldWriteUnion(): void
    {
        $union = new Union();
        $union->setBuilder($this->writer); // Union extends AbstractSetQuery, requires builder

        $select1 = new Select('user');
        $select1->setBuilder($this->writer);
        $union->add($select1);

        $select2 = new Select('user_email');
        $select2->setBuilder($this->writer);
        $union->add($select2);

        $expected = <<<SQL
SELECT user.* FROM user
UNION
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->unionWriter->write($union));
    }

    /**
     * @test
     */
    public function itShouldWriteUnionFromGenericBuilder(): void
    {
        $union = $this->writer->union(); // Renamed $unionAll to $union, this sets builder

        $select1 = new Select('user');
        $union->add($select1);

        $select2 = new Select('user_email');
        $union->add($select2);

        $expected = <<<SQL
SELECT user.* FROM user
UNION
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->writer->write($union));
    }

    /**
     * @test
     */
    public function itShouldNotResetPlaceholders(): void
    {
        $select1 = new Select('table1');
        $select1->setBuilder($this->writer);
        $select1->where()
            ->between('column', 1, 2)
            ->end();

        $select2 = new Select('table2');
        $select2->setBuilder($this->writer);
        $select2->where()
            ->between('column', 3, 4)
            ->end();

        $union = new Union();
        $union->setBuilder($this->writer);
        $union->add($select1)
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

        $this->assertEquals(str_replace("\r\n", "\n", $expectedSql), $this->writer->write($union));
        $this->assertEquals($expectedParams, $this->writer->getValues());
    }
}
