<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:36 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;

/**
 * Class SelectTest.
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Select
     */
    private $query;
    /**
     *
     */
    protected function setUp()
    {
        $this->query = new Select();
    }
    /**
     * @test
     */
    public function itShouldGetPartName()
    {
        $this->assertSame('SELECT', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldSetParentOrderByAlso()
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

        if (is_array($sorts)) {
            foreach ($sorts as $sort) {
                $order = (int)$sort['direction'] > 0 ? OrderBy::ASC : OrderBy::DESC;
                if (count($sort) == 5) {
                    $this->query->leftJoin(
                        $sort['table'],
                        $sort['joinBy'],
                        $sort['joinWith']
                    )->orderBy($sort['field'], $order);
                } else {
                    $this->query->orderBy($sort['field'], $order);
                }
            }
        }

        $returnedOrders = $this->query->getAllOrderBy();
        foreach ($returnedOrders as $id => $orderByObject) {
            $column = $orderByObject->getColumn();
            $table = $column->getTable();
            $expectedColumn = $sorts[$id]['field'];
            $expectedTable = array_key_exists('table', $sorts[$id]) ? $sorts[$id]['table'] : $parentTable;
            $expectedDirection = (int)$sorts[$id]['direction'] > 0 ? OrderBy::ASC : OrderBy::DESC;
            $this->assertSame($expectedColumn, $column->getName());
            $this->assertSame($expectedTable, $table->getName());
            $this->assertSame($expectedDirection, $orderByObject->getDirection());
        }
        $this->assertCount(count($sorts), $returnedOrders);
    }
}
