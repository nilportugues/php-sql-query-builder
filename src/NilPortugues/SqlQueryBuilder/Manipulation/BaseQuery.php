<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Syntax\OrderBy;
use NilPortugues\SqlQueryBuilder\Syntax\QueryPart;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\SqlQueryBuilder\Syntax\Table;
use NilPortugues\SqlQueryBuilder\Syntax\Where;

/**
 * Class BaseQuery
 * @package NilPortugues\SqlQueryBuilder
 */
abstract class BaseQuery implements Query, QueryPart
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    protected $whereOperator = "AND";

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var array
     */
    protected $joins = array();

    /**
     * @var int
     */
    protected $limitStart;

    /**
     * @var int
     */
    protected $limitCount;

    /**
     * @var array
     */
    protected $orderBy = array();

    /**
     * @return Where
     */
    protected function filter()
    {
        if (!isset($this->where)) {
            $this->where = QueryFactory::createWhere($this);
        }

        return $this->where;
    }

    /**
     * @return string
     */
    abstract public function partName();

    /**
     * @return Where
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        $newTable = array($this->table);

        return is_null($this->table) ? null : SyntaxFactory::createTable($newTable);
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param string $whereOperator
     *
     * @return Where
     */
    public function where($whereOperator = 'AND')
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        $this->where->conjunction($whereOperator);

        return $this->where;
    }

    /**
     * @return string
     */
    public function getWhereOperator()
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        return $this->where->getConjunction();
    }

    /**
     * @param        string $column
     * @param string $direction
     * @param null   $table
     *
     * @return $this
     */
    public function orderBy($column, $direction = OrderBy::ASC, $table = null)
    {
        $newColumn       = array($column);
        $column          = SyntaxFactory::createColumn($newColumn, is_null($table) ? $this->getTable() : $table);
        $this->orderBy[] = new OrderBy($column, $direction);

        return $this;
    }

    /**
     * @return array
     */
    public function getAllOrderBy()
    {
        $order = $this->orderBy;

        foreach ($this->joins as $join) {
            $order = array_merge($order, $join->getAllOrderBy());
        }

        return $order;
    }

    /**
     * @return int
     */
    public function getLimitCount()
    {
        return $this->limitCount;
    }

    /**
     * @return int
     */
    public function getLimitStart()
    {
        return $this->limitStart;
    }
}
