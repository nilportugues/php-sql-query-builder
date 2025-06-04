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
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\UnionAllWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll;
use PHPUnit\Framework\TestCase;

/**
 * Class UnionAllWriterTest.
 */
class UnionAllWriterTest extends TestCase
{
    private UnionAllWriter $unionAllWriter;
    private GenericBuilder $writer;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->unionAllWriter = new UnionAllWriter($this->writer);
    }

    protected function tearDown(): void
    {
        // Properties will be automatically garbage collected.
    }

    /**
     * @test
     */
    public function itShouldWriteUnionAll(): void
    {
        $union = new UnionAll();
        // UnionAll extends AbstractSetQuery. AbstractSetQuery needs setBuilder/getBuilder and getSql/__toString
        // if it's to be written directly by GenericBuilder.
        // Let's assume AbstractSetQuery (or UnionAll directly) will get these methods.
        $union->setBuilder($this->writer);


        $select1 = new Select('user');
        $select1->setBuilder($this->writer);
        $union->add($select1);

        $select2 = new Select('user_email');
        $select2->setBuilder($this->writer);
        $union->add($select2);

        $expected = <<<SQL
SELECT user.* FROM user
UNION ALL
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->unionAllWriter->write($union));
    }

    /**
     * @test
     */
    public function itShouldWriteUnionAllFromGenericBuilder(): void
    {
        $unionAll = $this->writer->unionAll(); // This sets the builder on UnionAll

        $select1 = new Select('user');
        // $select1->setBuilder($this->writer); // Not strictly needed if sub-queries are handled by write()
        $unionAll->add($select1);

        $select2 = new Select('user_email');
        // $select2->setBuilder($this->writer);
        $unionAll->add($select2);


        $expected = <<<SQL
SELECT user.* FROM user
UNION ALL
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->writer->write($unionAll));
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

        $union = new UnionAll();
        $union->setBuilder($this->writer);
        $union->add($select1)
            ->add($select2);

        $expectedSql = <<<SQL
SELECT table1.* FROM table1 WHERE (table1.column BETWEEN :v1 AND :v2)
UNION ALL
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
