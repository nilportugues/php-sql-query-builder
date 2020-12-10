<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/25/14
 * Time: 12:12 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;

/**
 * Class ColumnQuery.
 */
class ColumnQuery
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $columnSelects = [];

    /**
     * @var array
     */
    protected $columnValues = [];

    /**
     * @var array
     */
    protected $columnFuncs = [];

    /**
     * @var bool
     */
    protected $isCount = false;

    /**
     * @var Select
     */
    protected $select;

    /**
     * @var JoinQuery
     */
    protected $joinQuery;

    /**
     * @param Select    $select
     * @param JoinQuery $joinQuery
     * @param array     $columns
     */
    public function __construct(Select $select, JoinQuery $joinQuery, array $columns = null)
    {
        $this->select = $select;
        $this->joinQuery = $joinQuery;

        if (!isset($columns)) {
            $columns = array(Column::ALL);
        }

        if (\count($columns)) {
            $this->setColumns($columns);
        }
    }

    /**
     * @param     $start
     * @param int $count
     *
     * @return Select
     */
    public function limit($start, $count = 0)
    {
        return $this->select->limit($start, $count);
    }

    /**
     * @param string $whereOperator
     *
     * @return \NilPortugues\Sql\QueryBuilder\Syntax\Where
     */
    public function where($whereOperator = 'AND')
    {
        return $this->select->where($whereOperator);
    }

    /**
     * @param string $column
     * @param string $direction
     * @param null   $table
     *
     * @return Select
     */
    public function orderBy($column, $direction = OrderBy::ASC, $table = null)
    {
        return $this->select->orderBy($column, $direction, $table);
    }

    /**
     * @param string[] $columns
     *
     * @return Select
     */
    public function groupBy(array $columns)
    {
        return $this->select->groupBy($columns);
    }

    /**
     * Allows setting a Select query as a column value.
     *
     * @param array $column
     *
     * @return $this
     */
    public function setSelectAsColumn(array $column)
    {
        $this->columnSelects[] = $column;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnSelects()
    {
        return $this->columnSelects;
    }

    /**
     * Allows setting a value to the select statement.
     *
     * @param string $value
     * @param string $alias
     *
     * @return $this
     */
    public function setValueAsColumn($value, $alias)
    {
        $this->columnValues[$alias] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnValues()
    {
        return $this->columnValues;
    }

    /**
     * Allows calculation on columns using predefined SQL functions.
     *
     * @param string   $funcName
     * @param string[] $arguments
     * @param string   $alias
     *
     * @return $this
     */
    public function setFunctionAsColumn($funcName, array $arguments, $alias)
    {
        $this->columnFuncs[$alias] = ['func' => $funcName, 'args' => $arguments];

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnFuncs()
    {
        return $this->columnFuncs;
    }

    /**
     * @param string $columnName
     * @param string $alias
     *
     * @return $this
     */
    public function count($columnName = '*', $alias = '')
    {
        $table = $this->select->getTable();

        $count = 'COUNT(';
        $count .= ($columnName !== '*') ? "$table.{$columnName}" : '*';
        $count .= ')';

        if (isset($alias) && \strlen($alias) > 0) {
            $count .= ' AS "'.$alias.'"';
        }

        $this->columns = array($count);
        $this->isCount = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCount()
    {
        return $this->isCount;
    }

    /**
     * @return array
     */
    public function getAllColumns()
    {
        $columns = $this->getColumns();

        foreach ($this->joinQuery->getJoins() as $join) {
            $joinCols = $join->getAllColumns();
            $columns = \array_merge($columns, $joinCols);
        }

        return $columns;
    }

    /**
     * @return \NilPortugues\Sql\QueryBuilder\Syntax\Column
     *
     * @throws QueryException
     */
    public function getColumns()
    {
        if (\is_null($this->select->getTable())) {
            throw new QueryException('No table specified for the Select instance');
        }

        return SyntaxFactory::createColumns($this->columns, $this->select->getTable());
    }

    /**
     * Sets the column names used to write the SELECT statement.
     * If key is set, key is the column's alias. Value is always the column names.
     *
     * @param array $columns
     *
     * @return $this
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }
}
