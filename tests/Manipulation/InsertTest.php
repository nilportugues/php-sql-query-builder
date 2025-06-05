<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:37 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder; // For setBuilder
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use PHPUnit\Framework\TestCase;

/**
 * Class InsertTest.
 */
class InsertTest extends TestCase
{
    private Insert $query;

    protected function setUp(): void
    {
        $this->query = new Insert();
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('INSERT', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldSetValues(): void
    {
        $values = ['user_id' => 1, 'username' => 'nilportugues'];
        $this->query->setValues($values);
        $this->assertSame($values, $this->query->getValues());
    }

    /**
     * @test
     */
    public function itShouldGetColumns(): void
    {
        $values = ['user_id' => 1, 'username' => 'nilportugues'];
        $this->query->setTable('dummy_table'); // getColumns requires a table to be set
        $this->query->setValues($values);
        $columns = $this->query->getColumns();
        $this->assertNotEmpty($columns);
        $this->assertInstanceOf(Column::class, $columns[0]);
    }

    /**
     * @test
     */
    public function itShouldSetNullableValues(): void
    {
        $values = ['user_id' => 1, 'description' => null, 'isVisible' => false];
        $this->query->setValues($values);
        $this->assertSame($values, $this->query->getValues());
    }
}
