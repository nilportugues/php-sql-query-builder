<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:26 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder; // For setBuilder
use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use PHPUnit\Framework\TestCase;

/**
 * Class MinusTest.
 */
class MinusTest extends TestCase
{
    private Minus $query;
    private string $exceptionClass = QueryException::class;
    private GenericBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new GenericBuilder();
        $select1 = new Select('user');
        $select1->setBuilder($this->builder);

        $select2 = new Select('user_email');
        $select2->setBuilder($this->builder);

        $this->query = new Minus($select1, $select2);
        $this->query->setBuilder($this->builder); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('MINUS', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedGetTable(): void
    {
        $this->expectException($this->exceptionClass);
        $this->query->getTable();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedGetWhere(): void
    {
        $this->expectException($this->exceptionClass);
        $this->query->getWhere();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedWhere(): void
    {
        $this->expectException($this->exceptionClass);
        $this->query->where();
    }

    /**
     * @test
     */
    public function itShouldGetMinusSelects(): void
    {
        $expectedSelect1 = new Select('user');
        $expectedSelect1->setBuilder($this->builder); // Ensure it has a builder for comparison if needed

        $expectedSelect2 = new Select('user_email');
        $expectedSelect2->setBuilder($this->builder); // Ensure it has a builder

        // assertEquals on objects compares properties. If builder is a property and differs, it fails.
        // It's better to compare essential parts or SQL output if that's the goal.
        // For now, assuming property-wise comparison is intended and builder instances are the same.
        $this->assertEquals($expectedSelect1, $this->query->getFirst());
        $this->assertEquals($expectedSelect2, $this->query->getSecond());
    }
}
