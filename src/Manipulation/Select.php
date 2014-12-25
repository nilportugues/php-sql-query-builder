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
class Select extends AbstractBaseQuery
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $groupBy = [];

    /**
     * @var string
     */
    protected $camelCaseTableName = "";

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
     * @var JoinQuery
     */
    protected $joinQuery;

    /**
     * @param string $table
     * @param array  $columns
     */
    public function __construct($table = null, array $columns = null)
    {
        if (isset($table)) {
            $this->setTable($table);
        }

        if (!isset($columns)) {
            $columns = array(Column::ALL);
        }

        if (count($columns)) {
            $this->setColumns($columns);
        }

        $this->joinQuery = new JoinQuery($this);
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
     * @param string   $table
     * @param string   $selfColumn
     * @param string   $refColumn
     * @param string[] $columns
     *
     * @return Select
     */
    public function leftJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->joinQuery->leftJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * @param string   $table
     * @param string   $selfColumn
     * @param string   $refColumn
     * @param string[] $columns
     * @param string   $joinType
     *
     * @return Select
     */
    public function join(
        $table,
        $selfColumn = null,
        $refColumn = null,
        $columns = [],
        $joinType = null
    ) {
        return $this->joinQuery->join($table, $selfColumn, $refColumn, $columns, $joinType);
    }

    /**
     * WHERE constrains used for the ON clause of a (LEFT/RIGHT/INNER/CROSS) JOIN.
     *
     * @return Where
     */
    public function joinCondition()
    {
        return $this->joinQuery->joinCondition();
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
        return $this->joinQuery->addJoin($select, $selfColumn, $refColumn);
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
        return $this->joinQuery->isJoin($isJoin);
    }

    /**
     * @param string   $table
     * @param string   $selfColumn
     * @param string   $refColumn
     * @param string[] $columns
     *
     * @internal param null $selectClass
     *
     * @return Select
     */
    public function rightJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->joinQuery->rightJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * @param string   $table
     * @param string   $selfColumn
     * @param string   $refColumn
     * @param string[] $columns
     *
     * @return Select
     */
    public function crossJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->joinQuery->crossJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * @param string   $table
     * @param string   $selfColumn
     * @param string   $refColumn
     * @param string[] $columns
     *
     * @return Select
     */
    public function innerJoin($table, $selfColumn = null, $refColumn = null, $columns = [])
    {
        return $this->joinQuery->innerJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * Alias to joinCondition
     * @return Where
     */
    public function on()
    {
        return $this->joinQuery->joinCondition();
    }

    /**
     * @return boolean
     */
    public function isJoinSelect()
    {
        return $this->joinQuery->getIsJoin();
    }

    /**
     * @return array
     */
    public function getAllColumns()
    {
        $columns = $this->getColumns();

        foreach ($this->joinQuery->getJoins() as $join) {
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
     * Returns all the Where conditions to the BuilderInterface class in order to write the SQL WHERE statement.
     *
     * @return array
     */
    public function getAllWheres()
    {
        return $this->getAllOperation($this->where, 'getAllWheres');
    }

    /**
     * @param null|Where $data
     * @param string     $operation
     *
     * @return array
     */
    protected function getAllOperation($data, $operation)
    {
        $collection = [];

        if (!is_null($data)) {
            $collection[] = $data;
        }

        foreach ($this->joinQuery->getJoins() as $join) {
            $collection = array_merge($collection, $join->$operation());
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function getAllHavings()
    {
        return $this->getAllOperation($this->having, 'getAllHavings');
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
        $count .= ')';

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
        return $this->joinQuery->getAllJoins();
    }

    /**
     * @return array
     */
    public function getGroupBy()
    {
        return SyntaxFactory::createColumns($this->groupBy, $this->getTable());
    }

    /**
     * @param string[] $columns
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
        return $this->joinQuery->getJoinCondition();
    }

    /**
     * @return string
     */
    public function getJoinType()
    {
        return $this->joinQuery->getJoinType();
    }

    /**
     * @param string|null $joinType
     *
     * @return $this
     */
    public function setJoinType($joinType)
    {
        $this->joinQuery->setJoinType($joinType);

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

    /**
     * @return array
     */
    public function getAllOrderBy()
    {
        $order = $this->orderBy;

        foreach ($this->joinQuery->getJoins() as $join) {
            $order = array_merge($order, $join->getAllOrderBy());
        }

        return $order;
    }
}
