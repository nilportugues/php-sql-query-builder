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

use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\SqlQueryBuilder\Syntax\Table;
use NilPortugues\SqlQueryBuilder\Syntax\Where;

/**
 * Class Select
 * @package NilPortugues\SqlQueryBuilder\Manipulation
 */
class Select extends BaseQuery
{
    const JOIN_LEFT  = 'LEFT';
    const JOIN_RIGHT = 'RIGHT';
    const JOIN_INNER = 'INNER';
    const JOIN_CROSS = 'CROSS';

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Where
     */
    protected $joinCondition;

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $groupBy = array();

    /**
     * @var string
     */
    protected $camelCaseTableName = "";

    /**
     * @var bool
     */
    protected $isJoin = false;

    /**
     * @var string
     */
    protected $joinType;

    /**
     * @var Where
     */
    protected $having;

    /**
     * @var string
     */
    protected $havingOperator = "AND";

    /**
     * @var bool
     */
    protected $isDistinct = false;

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var bool
     */
    protected $isCount = false;

    /**
     * @var array
     */
    protected $columnSelects = array();

    /**
     * @var array
     */
    protected $columnValues = array();

    /**
     * @var array
     */
    protected $columnFuncs = array();

    /**
     * @param string $table
     * @param array  $columns
     */
    public function __construct($table = null, $columns = array(Column::ALL))
    {
        if (isset($table)) {
            $this->setTable($table);
        }

        if (count($columns)) {
            $this->setColumns($columns);
        }
    }

    /**
     * This __clone method will create an exact clone but without the object references due to the fact these
     * are lost in the process of serialization and un-serialization.
     *
     * @return Select
     */
    public function __clone()
    {
        return unserialize(serialize($this));
    }

    /**
     * @return string
     */
    public function partName()
    {
        return 'SELECT';
    }

    /**
     * @param       $table
     * @param null  $selfColumn
     * @param null  $refColumn
     * @param array $columns
     *
     * @return Select
     */
    public function leftJoin($table, $selfColumn = null, $refColumn = null, $columns = array())
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_LEFT);
    }

    /**
     * @param        $table
     * @param null   $selfColumn
     * @param null   $refColumn
     * @param array  $columns
     * @param string $joinType
     *
     * @return Select
     */
    public function join(
        $table,
        $selfColumn = null,
        $refColumn = null,
        $columns = array(),
        $joinType = null
    ) {
        $newTable = array($table);
        $table    = SyntaxFactory::createTable($newTable);
        $key      = $table->getCompleteName();

        if (!isset($this->joins[$key])) {

            $select = QueryFactory::createSelect($table);
            $select->setColumns($columns);
            $select->setJoinType($joinType);
            $this->addJoin($select, $selfColumn, $refColumn);
        }

        return $this->joins[$key];

    }

    /**
     * WHERE constrains used for the ON clause of a (LEFT/RIGHT/INNER/CROSS) JOIN.
     *
     * @return Where
     */
    public function joinCondition()
    {
        if (!isset($this->joinCondition)) {
            $this->joinCondition = QueryFactory::createWhere($this);
        }

        return $this->joinCondition;
    }

    /**
     * @param Select $select
     * @param string $selfColumn
     * @param string $refColumn
     *
     * @return Select
     */
    public function addJoin(Select $select, $selfColumn, $refColumn)
    {
        $select->isJoin(true);
        $key = $select->getTable()->getCompleteName();

        if (!isset($this->joins[$key])) {

            $newColumn = array($selfColumn);
            $select->joinCondition()->equals($refColumn, SyntaxFactory::createColumn($newColumn, $this->getTable()));
            $this->joins[$key] = $select;
        }

        return $this->joins[$key];
    }

    /**
     * Transforms Select in a joint
     *
     * @param bool $isJoin
     *
     * @return $this
     */
    public function isJoin($isJoin = true)
    {
        $this->isJoin = $isJoin;

        return $this;
    }

    /**
     * @param       $table
     * @param null  $selfColumn
     * @param null  $refColumn
     * @param array $columns
     *
     * @internal param null $selectClass
     *
     * @return Select
     */
    public function rightJoin($table, $selfColumn = null, $refColumn = null, $columns = array())
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_RIGHT);
    }

    /**
     * @param       $table
     * @param null  $selfColumn
     * @param null  $refColumn
     * @param array $columns
     *
     * @return Select
     */
    public function crossJoin($table, $selfColumn = null, $refColumn = null, $columns = array())
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_CROSS);
    }

    /**
     * @param       $table
     * @param null  $selfColumn
     * @param null  $refColumn
     * @param array $columns
     *
     * @return Select
     */
    public function innerJoin($table, $selfColumn = null, $refColumn = null, $columns = array())
    {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_INNER);
    }

    /**
     * Alias to joinCondition
     * @return Where
     */
    public function on()
    {
        return $this->joinCondition();
    }

    /**
     * @return boolean
     */
    public function isJoinSelect()
    {
        return $this->isJoin;
    }

    /**
     * @return array
     */
    public function getAllColumns()
    {
        $columns = $this->getColumns();

        foreach ($this->joins as $join) {
            $joinCols = $join->getAllColumns();
            $columns  = array_merge($columns, $joinCols);
        }

        return $columns;
    }

    /**
     * @return \NilPortugues\SqlQueryBuilder\Syntax\Column
     * @throws QueryException
     */
    public function getColumns()
    {
        if (is_null($this->table)) {
            throw new QueryException("No table specified for the Select instance");
        }

        return SyntaxFactory::createColumns($this->columns, $this->getTable());
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
     * @param       string $funcName
     * @param string[] $arguments
     * @param       string $alias
     *
     * @return $this
     */
    public function setFunctionAsColumn($funcName, array $arguments, $alias)
    {
        $this->columnFuncs[$alias] = array('func' => $funcName, 'args' => $arguments);

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
     * Returns all the Where conditions to the BuilderInterface class in order to write the SQL WHERE statement.
     *
     * @return array
     */
    public function getAllWheres()
    {
        $wheres = array();

        if (!is_null($this->where)) {
            $wheres[] = $this->where;
        }

        foreach ($this->joins as $join) {
            $wheres = array_merge($wheres, $join->getAllWheres());
        }

        return $wheres;
    }

    /**
     * @return array
     */
    public function getAllHavings()
    {
        $havings = array();

        if (!is_null($this->having)) {
            $havings[] = $this->having;
        }

        /** @var $join Select */
        foreach ($this->joins as $join) {
            $havings = array_merge($havings, $join->getAllHavings());
        }

        return $havings;
    }

    /**
     * @param string $columnName
     * @param string $alias
     *
     * @return $this
     */
    public function count($columnName = '*', $alias = '')
    {
        $count = 'COUNT(';
        $count .= ($columnName !== '*') ? "$this->table.{$columnName}" : '*';
        $count .=')';

        if (isset($alias) && strlen($alias)>0) {
            $count .= " AS '{$alias}'";
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
     * @param integer $start
     * @param $count
     *
     * @return $this
     */
    public function limit($start, $count = 0)
    {
        $this->limitStart = $start;
        $this->limitCount = $count;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllJoins()
    {
        $joins = $this->joins;

        foreach ($this->joins as $join) {
            $joins = array_merge($joins, $join->getAllJoins());
        }

        return $joins;
    }

    /**
     * @return array
     */
    public function getGroupBy()
    {
        return SyntaxFactory::createColumns($this->groupBy, $this->getTable());
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function groupBy(array $columns)
    {
        $this->groupBy = $columns;

        return $this;
    }

    /**
     * @return Where
     */
    public function getJoinCondition()
    {
        return $this->joinCondition;
    }

    /**
     * @return string
     */
    public function getJoinType()
    {
        return $this->joinType;
    }

    /**
     * @param string|null $joinType
     *
     * @return $this
     */
    public function setJoinType($joinType)
    {
        $this->joinType = $joinType;

        return $this;
    }

    /**
     * @param $havingOperator
     *
     * @throws QueryException
     * @return Where
     */
    public function having($havingOperator = 'AND')
    {
        if (!isset($this->having)) {
            $this->having = QueryFactory::createWhere($this);
        }

        if (!in_array($havingOperator, array(Where::CONJUNCTION_AND, Where::CONJUNCTION_OR))) {
            throw new QueryException(
                "Invalid conjunction specified, must be one of AND or OR, but '".$havingOperator."' was found."
            );
        }

        $this->havingOperator = $havingOperator;

        return $this->having;
    }

    /**
     * @return string
     */
    public function getHavingOperator()
    {
        return $this->havingOperator;
    }

    /**
     * @return $this
     */
    public function distinct()
    {
        $this->isDistinct = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDistinct()
    {
        return $this->isDistinct;
    }
}
