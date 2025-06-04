<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:36 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder; // For setBuilder
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use PHPUnit\Framework\TestCase;

/**
 * Class SelectTest.
 */
class SelectTest extends TestCase
{
    private Select $query;

    protected function setUp(): void
    {
        $this->query = new Select();
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('SELECT', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldSetParentOrderByAlso(): void
    {
        $columns = [
            'id',
            'phase_id',
            'league_id',
            'date',
        ];
        $parentTable = 'events';
        $this->query->setTable($parentTable);
        $this->query->setColumns($columns);

        $sorts = [
            [
                'field' => 'league_id',
                'direction' => 1,
            ],
            [
                'field' => 'start_date',
                'direction' => 0,
                'table' => 'phases',
                'joinBy' => 'phase_id',
                'joinWith' => 'id',
            ],
            [
                'field' => 'date',
                'direction' => 1,
            ],
        ];

        // The `if (is_array($sorts))` check is redundant as $sorts is always an array here.
        foreach ($sorts as $sort) {
            $order = (int) $sort['direction'] > 0 ? OrderBy::ASC : OrderBy::DESC;
            // Check for 'table' key to determine if it's a join orderBy
            if (isset($sort['table']) && isset($sort['joinBy']) && isset($sort['joinWith'])) {
                $this->query->leftJoin(
                    (string) $sort['table'],
                    (string) $sort['joinBy'],
                    (string) $sort['joinWith']
                )->orderBy((string) $sort['field'], $order, (string) $sort['table']); // Specify table for orderBy on joined column
            } else {
                $this->query->orderBy((string) $sort['field'], $order);
            }
        }

        $returnedOrders = $this->query->getAllOrderBy();
        foreach ($returnedOrders as $id => $orderByObject) {
            $column = $orderByObject->getColumn();
            $table = $column->getTable();
            $this->assertNotNull($table, "Table for OrderBy column {$column->getName()} should not be null");

            $expectedColumn = (string) $sorts[$id]['field'];
            $expectedTable = isset($sorts[$id]['table']) ? (string) $sorts[$id]['table'] : $parentTable;
            $expectedDirection = (int) $sorts[$id]['direction'] > 0 ? OrderBy::ASC : OrderBy::DESC;

            $this->assertSame($expectedColumn, $column->getName());
            $this->assertSame($expectedTable, $table->getName());
            $this->assertSame($expectedDirection, $orderByObject->getDirection());
        }
        $this->assertCount(\count($sorts), $returnedOrders);
    }
}
