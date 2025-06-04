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
use NilPortugues\Sql\QueryBuilder\Manipulation\Intersect;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use PHPUnit\Framework\TestCase;

/**
 * Class IntersectTest.
 */
class IntersectTest extends TestCase
{
    private Intersect $query;
    private string $exceptionClass = QueryException::class;

    protected function setUp(): void
    {
        $this->query = new Intersect();
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('INTERSECT', $this->query->partName());
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
    public function itShouldGetIntersectSelects(): void
    {
        $this->assertEquals([], $this->query->getIntersects());

        $select1 = new Select('user');
        $select1->setBuilder(new GenericBuilder()); // Set builder if Select needs it for any operations

        $select2 = new Select('user_email');
        $select2->setBuilder(new GenericBuilder()); // Set builder

        $this->query->add($select1);
        $this->query->add($select2);

        $this->assertEquals([$select1, $select2], $this->query->getIntersects());
    }
}
