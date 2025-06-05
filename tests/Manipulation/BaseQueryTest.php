<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/7/14
 * Time: 11:44 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder; // For setBuilder
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use NilPortugues\Tests\Sql\QueryBuilder\Manipulation\Resources\DummyQuery;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseQueryTest.
 */
class BaseQueryTest extends TestCase
{
    private DummyQuery $query;
    private string $whereClass = Where::class;

    protected function setUp(): void
    {
        $this->query = new DummyQuery();
        $this->query->setTable('tablename');
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    protected function tearDown(): void
    {
        // No need to null $this->query, PHPUnit handles test isolation.
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSetTableName(): void
    {
        $table = $this->query->getTable();
        $this->assertNotNull($table);
        $this->assertSame('tablename', $table->getName());
    }

    /**
     * @test
     */
    public function itShouldGetWhere(): void
    {
        $this->assertNull($this->query->getWhere());

        $this->query->where(); // This initializes the Where object
        $whereInstance = $this->query->getWhere();
        $this->assertInstanceOf($this->whereClass, $whereInstance);
    }

    /**
     * @test
     */
    public function itShouldGetWhereOperator(): void
    {
        $this->assertSame('AND', $this->query->getWhereOperator()); // Default

        $this->query->where('OR');
        $this->assertSame('OR', $this->query->getWhereOperator());
    }
}
