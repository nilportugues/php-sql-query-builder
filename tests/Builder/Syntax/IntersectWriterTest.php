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
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\IntersectWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\Intersect;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use PHPUnit\Framework\TestCase;

/**
 * Class IntersectWriterTest.
 */
class IntersectWriterTest extends TestCase
{
    private GenericBuilder $writer;
    private IntersectWriter $intersectWriter;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->intersectWriter = new IntersectWriter($this->writer);
    }

    protected function tearDown(): void
    {
        // Properties will be automatically garbage collected if not holding circular references.
        // Explicitly nulling is not strictly necessary in PHP 8+ for simple objects like these
        // unless for specific resource cleanup or to break circular refs not handled by GC.
    }

    /**
     * @test
     */
    public function itShouldWriteIntersect(): void
    {
        $intersect = new Intersect();
        $intersect->setBuilder($this->writer); // Intersect needs a builder instance

        $intersect->add(new Select('user'));
        $intersect->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
INTERSECT
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->intersectWriter->write($intersect));
    }

    /**
     * @test
     */
    public function itShouldWriteIntersectFromGenericBuilder(): void
    {
        $intersect = $this->writer->intersect(); // This already sets the builder

        $intersect->add(new Select('user'));
        $intersect->add(new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
INTERSECT
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->writer->write($intersect));
    }

    /**
     * @test
     */
    public function itShouldNotResetPlaceholders(): void
    {
        $select1 = (new Select('table1'));
        $select1->setBuilder($this->writer); // Set builder for placeholder generation context
        $select1->where()
            ->between('column', 1, 2)
            ->end();

        $select2 = (new Select('table2'));
        $select2->setBuilder($this->writer); // Set builder
        $select2->where()
            ->between('column', 3, 4)
            ->end();

        $intersect = (new Intersect());
        $intersect->setBuilder($this->writer); // Set builder
        $intersect->add($select1)
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

        $this->assertEquals(str_replace("\r\n", "\n", $expectedSql), $this->writer->write($intersect));
        $this->assertEquals($expectedParams, $this->writer->getValues());
    }
}
