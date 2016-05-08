<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;

/**
 * Class Select.
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
    protected $groupBy = [];

    /**
     * @var string
     */
    protected $camelCaseTableName = '';

    /**
     * @var Where
     */
    protected $having;

    /**
     * @var string
     */
    protected $havingOperator = 'AND';

    /**
     * @var bool
     */
    protected $isDistinct = false;

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var JoinQuery
     */
    protected $joinQuery;

    /**
     * @var ColumnQuery
     */
    protected $columnQuery;

    /**
     * @var ParentQuery
     */
    protected $parentQuery;

    /**
     * @param string $table
     * @param array  $columns
     */
    public function __construct($table = null, array $columns = null)
    {
        if (isset($table)) {
            $this->setTable($table);
        }

        $this->joinQuery = new JoinQuery($this);
        $this->columnQuery = new ColumnQuery($this, $this->joinQuery, $columns);
    }

    /**
     * This __clone method will create an exact clone but without the object references due to the fact these
     * are lost in the process of serialization and un-serialization.
     *
     * @return Select
     */
    public function __clone()
    {
        return \unserialize(\serialize($this));
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
     * Transforms Select in a joint.
     *
     * @param bool $isJoin
     *
     * @return JoinQuery
     */
    public function isJoin($isJoin = true)
    {
        return $this->joinQuery->setJoin($isJoin);
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
     * Alias to joinCondition.
     *
     * @return Where
     */
    public function on()
    {
        return $this->joinQuery->joinCondition();
    }

    /**
     * @return bool
     */
    public function isJoinSelect()
    {
        return $this->joinQuery->isJoin();
    }

    /**
     * @return array
     */
    public function getAllColumns()
    {
        return $this->columnQuery->getAllColumns();
    }

    /**
     * @return \NilPortugues\Sql\QueryBuilder\Syntax\Column
     *
     * @throws QueryException
     */
    public function getColumns()
    {
        return $this->columnQuery->getColumns();
    }

    /**
     * Sets the column names used to write the SELECT statement.
     * If key is set, key is the column's alias. Value is always the column names.
     *
     * @param string[] $columns
     *
     * @return ColumnQuery
     */
    public function setColumns(array $columns)
    {
        return $this->columnQuery->setColumns($columns);
    }

    /**
     * Allows setting a Select query as a column value.
     *
     * @param array $column
     *
     * @return ColumnQuery
     */
    public function setSelectAsColumn(array $column)
    {
        return $this->columnQuery->setSelectAsColumn($column);
    }

    /**
     * @return array
     */
    public function getColumnSelects()
    {
        return $this->columnQuery->getColumnSelects();
    }

    /**
     * Allows setting a value to the select statement.
     *
     * @param string $value
     * @param string $alias
     *
     * @return ColumnQuery
     */
    public function setValueAsColumn($value, $alias)
    {
        return $this->columnQuery->setValueAsColumn($value, $alias);
    }

    /**
     * @return array
     */
    public function getColumnValues()
    {
        return $this->columnQuery->getColumnValues();
    }

    /**
     * Allows calculation on columns using predefined SQL functions.
     *
     * @param string   $funcName
     * @param string[] $arguments
     * @param string   $alias
     *
     * @return ColumnQuery
     */
    public function setFunctionAsColumn($funcName, array $arguments, $alias)
    {
        return $this->columnQuery->setFunctionAsColumn($funcName, $arguments, $alias);
    }

    /**
     * @return array
     */
    public function getColumnFuncs()
    {
        return $this->columnQuery->getColumnFuncs();
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
            $collection = \array_merge($collection, $join->$operation());
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
     * @return ColumnQuery
     */
    public function count($columnName = '*', $alias = '')
    {
        return $this->columnQuery->count($columnName, $alias);
    }

    /**
     * @return bool
     */
    public function isCount()
    {
        return $this->columnQuery->isCount();
    }

    /**
     * @param int $start
     * @param     $count
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
     *
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
        return $this->orderBy;
    }

    /**
     * @return ParentQuery
     */
    public function getParentQuery()
    {
        return $this->parentQuery;
    }

    /**
     * @param Select $parentQuery
     *
     * @return $this
     */
    public function setParentQuery(Select $parentQuery)
    {
        $this->parentQuery = $parentQuery;

        return $this;
    }

    /**
     * @param string $column
     * @param string $direction
     * @param null   $table
     *
     * @return $this
     */
    public function orderBy($column, $direction = OrderBy::ASC, $table = null)
    {
        $current = parent::orderBy($column, $direction, $table);
        if ($this->getParentQuery() != null) {
            $this->getParentQuery()->orderBy($column, $direction, \is_null($table) ? $this->getTable() : $table);
        }
        return $current;
    }
}
