<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\SqlQueryBuilder\Syntax;

use NilPortugues\SqlQueryBuilder\Manipulation\QueryInterface;
use NilPortugues\SqlQueryBuilder\Manipulation\QueryException;
use NilPortugues\SqlQueryBuilder\Manipulation\QueryFactory;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class Where
 * @package NilPortugues\SqlQueryBuilder\Syntax
 */
class Where
{
    const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';
    const OPERATOR_GREATER_THAN          = '>';
    const OPERATOR_LESS_THAN_OR_EQUAL    = '<=';
    const OPERATOR_LESS_THAN             = '<';
    const OPERATOR_LIKE                  = 'LIKE';
    const OPERATOR_NOT_LIKE              = 'NOT LIKE';
    const OPERATOR_EQUAL                 = '=';
    const OPERATOR_NOT_EQUAL             = '<>';
    const CONJUNCTION_AND                = 'AND';
    const CONJUNCTION_OR                 = 'OR';

    /**
     * @var array
     */
    private $comparisons = array();

    /**
     * @var array
     */
    private $betweens = array();

    /**
     * @var array
     */
    private $isNull = array();

    /**
     * @var array
     */
    private $isNotNull = array();

    /**
     * @var array
     */
    private $booleans = array();

    /**
     * @var array
     */
    private $match = array();

    /**
     * @var array
     */
    private $ins = array();

    /**
     * @var array
     */
    private $notIns = array();

    /**
     * @var array
     */
    private $subWheres = array();

    /**
     * @var string
     */
    private $conjunction = self::CONJUNCTION_AND;

    /**
     * @var  QueryInterface
     */
    private $query;

    /**
     * @var Table
     */
    private $table;

    /**
     * @param QueryInterface $query
     */
    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    /**
     * Deep copy for nested references
     * @return mixed
     */
    public function __clone()
    {
        return unserialize(serialize($this));
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (
            !isset($this->comparisons)
            && !isset($this->booleans)
            && !isset($this->betweens)
            && !isset($this->isNotNull)
            && !isset($this->isNull)
            && !isset($this->ins)
            && !isset($this->notIns)
            && !isset($this->subWheres)
        );
    }

    /**
     * @return string
     */
    public function getConjunction()
    {
        return $this->conjunction;
    }

    /**
     * @return array
     */
    public function getSubWheres()
    {
        return $this->subWheres;
    }

    /**
     * @param $operator
     *
     * @return Where
     */
    public function subWhere($operator = 'OR')
    {
        /** @var Where $filter */
        $filter = QueryFactory::createWhere($this->query);

        $filter->conjunction($operator);
        $filter->setTable($this->getTable());

        $this->subWheres[] = $filter;

        return $filter;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->query->getTable();
    }

    /**
     * Used for subWhere query building
     *
     * @param Table $table string
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * equals alias
     *
     * @param $column
     * @param integer $value
     *
     * @return static
     */
    public function eq($column, $value)
    {
        return $this->equals($column, $value);
    }

    /**
     * @param $column
     * @param $value
     *
     * @return static
     */
    public function equals($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_EQUAL);
    }

    /**
     * @param $column
     * @param $value
     * @param string $operator
     *
     * @return $this
     */
    public function compare($column, $value, $operator)
    {
        $column = $this->prepareColumn($column);

        $this->comparisons[] = array("subject" => $column, "conjunction" => $operator, "target" => $value);

        return $this;
    }

    /**
     * @param $column
     *
     * @return Column
     */
    private function prepareColumn($column)
    {
        //This condition handles the "Select as a a column" special case.
        if ($column instanceof Select) {
            return $column;
        }
        $newColumn = array($column);

        return SyntaxFactory::createColumn($newColumn, $this->getTable());
    }

    /**
     * @param string  $column
     * @param integer $value
     *
     * @return static
     */
    public function notEquals($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_NOT_EQUAL);
    }

    /**
     * @param string  $column
     * @param integer $value
     *
     * @return static
     */
    public function greaterThan($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_GREATER_THAN);
    }

    /**
     * @param string  $column
     * @param integer $value
     *
     * @return static
     */
    public function greaterThanOrEqual($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_GREATER_THAN_OR_EQUAL);
    }

    /**
     * @param string  $column
     * @param integer $value
     *
     * @return static
     */
    public function lessThan($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_LESS_THAN);
    }

    /**
     * @param string  $column
     * @param integer $value
     *
     * @return static
     */
    public function lessThanOrEqual($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_LESS_THAN_OR_EQUAL);
    }

    /**
     * @param string $column
     * @param $value
     *
     * @return static
     */
    public function like($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_LIKE);
    }

    /**
     * @param string  $column
     * @param integer $value
     *
     * @return static
     */
    public function notLike($column, $value)
    {
        return $this->compare($column, $value, self::OPERATOR_NOT_LIKE);
    }

    /**
     * @param           $column
     * @param integer[] $values
     *
     * @return static
     */
    public function match(array $columns, array $values)
    {
        $this->match[] = array(
            'columns' => $columns,
            'values'  => $values,
            'mode'    => 'natural',
        );

        return $this;
    }

    /**
     * @param  array $columns
     * @param  array $values
     * @return $this
     */
    public function matchBoolean(array $columns, array $values)
    {
        $this->match[] = array(
            'columns' => $columns,
            'values'  => $values,
            'mode'    => 'boolean',
        );

        return $this;
    }

    /**
     * @param array $columns
     * @param array $values
     * @return $this
     */
    public function matchWithQueryExpansion(array $columns, array $values)
    {
        $this->match[] = array(
            'columns' => $columns,
            'values'  => $values,
            'mode'    => 'query_expansion'
        );

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function in($column, array $values)
    {
        $this->ins[$column] = $values;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function notIn($column, array $values)
    {
        $this->notIns[$column] = $values;

        return $this;
    }

    /**
     * @param $column
     * @param $a
     * @param $b
     * @return $this
     */
    public function between($column, $a, $b)
    {
        $column           = $this->prepareColumn($column);
        $this->betweens[] = array("subject" => $column, "a" => $a, "b" => $b);

        return $this;
    }

    /**
     * @param string $column
     *
     * @return static
     */
    public function isNull($column)
    {
        $column         = $this->prepareColumn($column);
        $this->isNull[] = array("subject" => $column);

        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function isNotNull($column)
    {
        $column            = $this->prepareColumn($column);
        $this->isNotNull[] = array("subject" => $column);

        return $this;
    }

    /**
     * @param $column
     * @param $value
     * @return $this
     */
    public function addBitClause($column, $value)
    {
        $column           = $this->prepareColumn($column);
        $this->booleans[] = array("subject" => $column, "value" => ($value));

        return $this;
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return $this->match;
    }

    /**
     * @return array
     */
    public function getIns()
    {
        return $this->ins;
    }

    /**
     * @return array
     */
    public function getNotIns()
    {
        return $this->notIns;
    }

    /**
     * @param string $operator
     *
     * @return $this
     * @throws QueryException
     */
    public function conjunction($operator)
    {
        if (!in_array($operator, array(self::CONJUNCTION_AND, self::CONJUNCTION_OR))) {
            throw new QueryException(
                "Invalid conjunction specified, must be one of AND or OR, but '".$operator."' was found."
            );
        }
        $this->conjunction = $operator;

        return $this;
    }

    /**
     * @return array
     */
    public function getBetweens()
    {
        return $this->betweens;
    }

    /**
     * @return array
     */
    public function getBooleans()
    {
        return $this->booleans;
    }

    /**
     * @return array
     */
    public function getComparisons()
    {
        return $this->comparisons;
    }

    /**
     * @return array
     */
    public function getNotNull()
    {
        return $this->isNotNull;
    }

    /**
     * @return array
     */
    public function getNull()
    {
        return $this->isNull;
    }
}
