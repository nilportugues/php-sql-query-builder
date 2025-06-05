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
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\MinusWriter;
use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use PHPUnit\Framework\TestCase;

/**
 * Class MinusWriterTest.
 */
class MinusWriterTest extends TestCase
{
    private MinusWriter $minusWriter;
    private GenericBuilder $writer;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->minusWriter = new MinusWriter($this->writer);
    }

    protected function tearDown(): void
    {
        // Properties will be automatically garbage collected.
    }

    /**
     * @test
     */
    public function itShouldWriteMinus(): void
    {
        $select1 = new Select('user');
        $select1->setBuilder($this->writer); // Set builder for Select

        $select2 = new Select('user_email');
        $select2->setBuilder($this->writer); // Set builder for Select

        $minus = new Minus($select1, $select2);
        $minus->setBuilder($this->writer); // Minus needs a builder instance

        $expected = <<<SQL
SELECT user.* FROM user
MINUS
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->minusWriter->write($minus));
    }

    /**
     * @test
     */
    public function itShouldWriteMinusFromGenericBuilder(): void // Test name was "itShouldWriteUnionAllFromGenericBuilder"
    {
        $select1 = new Select('user');
        // No need to set builder on select1/select2 if GenericBuilder::minus handles it for sub-queries,
        // but GenericBuilder::write($minus) is the ultimate test.
        // $select1->setBuilder($this->writer);

        $select2 = new Select('user_email');
        // $select2->setBuilder($this->writer);

        $minus = $this->writer->minus($select1, $select2); // This already sets the builder on Minus

        $expected = <<<SQL
SELECT user.* FROM user
MINUS
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $this->writer->write($minus));
    }
}
