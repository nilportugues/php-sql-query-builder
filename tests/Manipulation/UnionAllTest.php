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
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll;
use PHPUnit\Framework\TestCase;

/**
 * Class UnionAllTest.
 */
class UnionAllTest extends TestCase
{
    private UnionAll $query;
    private string $exceptionClass = QueryException::class;

    protected function setUp(): void
    {
        $this->query = new UnionAll();
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('UNION ALL', $this->query->partName());
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
    public function itShouldGetUnionSelects(): void // Renamed from itShouldGetIntersectSelects
    {
        $this->assertEquals([], $this->query->getUnions());

        $builder = new GenericBuilder(); // Builder for the Select objects

        $select1 = new Select('user');
        $select1->setBuilder($builder);

        $select2 = new Select('user_email');
        $select2->setBuilder($builder);

        $this->query->add($select1);
        $this->query->add($select2);

        $this->assertEquals([$select1, $select2], $this->query->getUnions());
    }
}
