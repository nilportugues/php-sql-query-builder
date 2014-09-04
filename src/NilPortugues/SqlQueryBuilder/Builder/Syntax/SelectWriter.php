<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/11/14
 * Time: 1:50 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;
use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\OrderBy;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;

/**
 * Class SelectWriter
 * @package NilPortugues\SqlQueryBuilder\BuilderInterface\Syntax
 */
class SelectWriter
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var PlaceholderWriter
     */
    private $placeholderWriter;

    /**
     * @var ColumnWriter
     */
    private $columnWriter;

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(GenericBuilder $writer, PlaceholderWriter $placeholder)
    {
        $this->writer            = $writer;
        $this->placeholderWriter = $placeholder;

        $this->columnWriter = WriterFactory::createColumnWriter($this->writer, $placeholder);
    }

    /**
     * @param        $alias
     * @param Select $select
     *
     * @return Column
     */
    public function selectToColumn($alias, Select $select)
    {
        $selectAsColumn = $this->writeSelect($select);

        if (!empty($selectAsColumn)) {
            $selectAsColumn = '('.$selectAsColumn.')';
        }

        $alias  = $this->writer->writeAlias($alias);
        $column = array($alias => $selectAsColumn);

        return SyntaxFactory::createColumn($column, null);
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelect(Select $select)
    {
        if ($select->isJoinSelect()) {
            return $this->writer->writeJoin($select);
        }

        $parts = array("SELECT");

        if ($select->isDistinct()) {
            $parts[] = "DISTINCT";
        }

        $parts[] = $this->writeSelectColumns($select);
        $parts[] = $this->writeSelectFrom($select);
        $parts[] = $this->writeSelectJoins($select);
        $parts[] = $this->writeSelectWhere($select);
        $parts[] = $this->writeSelectGroupBy($select);
        $parts[] = $this->writeSelectHaving($select);
        $parts[] = $this->writeSelectOrderBy($select);
        $parts[] = $this->writeSelectLimit($select);

        $parts = array_filter($parts);

        return implode(" ", $parts);
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelectColumns(Select $select)
    {
        if ($select->isCount() === false) {

            $tableColumns    = $select->getAllColumns();
            $selectAsColumns = $this->columnWriter->writeSelectsAsColumns($select);
            $valueAsColumns  = $this->columnWriter->writeValueAsColumns($select);
            $funcAsColumns   = $this->columnWriter->writeFuncAsColumns($select);

            $columns = array_merge($tableColumns, $selectAsColumns, $valueAsColumns, $funcAsColumns);

            array_walk(
                $columns,
                function (&$column) {
                    $column = $this->columnWriter->writeColumnWithAlias($column);
                }
            );

            $columnList = implode(", ", $columns);

        } else {

            $columns    = $select->getColumns();
            $column     = array_pop($columns);
            $columnList = $column->getName();
        }

        return $columnList;
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelectFrom(Select $select)
    {
        return "FROM ".$this->writer->writeTableWithAlias($select->getTable());
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelectJoins(Select $select)
    {
        $str   = "";
        $joins = $select->getAllJoins();

        if (!empty($joins)) {

            array_walk(
                $joins,
                function (&$join) {
                    $join = $this->writer->writeJoin($join);
                }
            );

            $separator = " ";
            $str       = implode($separator, $joins);
        }

        return $str;
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelectWhere(Select $select)
    {
        $str    = "";
        $wheres = $this->writeSelectWheres($select->getAllWheres());
        $wheres = array_filter($wheres);

        if (count($wheres) > 0) {

            $str       = "WHERE ";
            $separator = " ".$this->writer->writeConjunction($select->getWhereOperator())." ";

            $str .= implode($separator, $wheres);
        }

        return $str;
    }

    /**
     * @param array $wheres
     *
     * @return array
     */
    protected function writeSelectWheres(array $wheres)
    {
        $whereWriter = WriterFactory::createWhereWriter($this->writer, $this->placeholderWriter);

        array_walk(
            $wheres,
            function (&$where) use (&$whereWriter) {

                $where = $whereWriter->writeWhere($where);
            }
        );

        return $wheres;
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelectGroupBy(Select $select)
    {
        $str = "";
        if (count($select->getGroupBy())) {

            $groupCols = $select->getGroupBy();

            array_walk(
                $groupCols,
                function (&$column) {
                    $column = $this->columnWriter->writeColumn($column);
                }
            );

            $str = "GROUP BY ";
            $str .= implode(", ", $groupCols);
        }

        return $str;
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeSelectHaving(Select $select)
    {
        $str = "";

        if (count($havingArray = $select->getAllHavings()) > 0) {

            $placeholder = $this->placeholderWriter;
            $writer      = $this->writer;

            array_walk(
                $havingArray,
                function (&$having) use ($select, $writer, $placeholder) {

                    $whereWriter = WriterFactory::createWhereWriter($writer, $placeholder);
                    $clauses     = $whereWriter->writeWhereClauses($having);
                    $having      = implode($this->writer->writeConjunction($select->getHavingOperator()), $clauses);
                }
            );

            $str       = "HAVING ";
            $separator = " ".$select->getHavingOperator()." ";

            $str .= implode($separator, $havingArray);
        }

        return $str;
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    protected function writeSelectOrderBy(Select $select)
    {
        $str = "";
        if (count($select->getAllOrderBy())) {

            $orderByArray = $select->getAllOrderBy();
            array_walk(
                $orderByArray,
                function (&$orderBy) {
                    $orderBy = $this->writeOrderBy($orderBy);
                }
            );

            $str = "ORDER BY ";
            $str .= implode(", ", $orderByArray);
        }

        return $str;
    }

    /**
     * @param OrderBy $orderBy
     *
     * @return string
     */
    public function writeOrderBy(OrderBy $orderBy)
    {
        $column = $this->columnWriter->writeColumn($orderBy->getColumn());

        return $column.' '.$orderBy->getDirection();
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    protected function writeSelectLimit(Select $select)
    {
        $mask = (is_null($select->getLimitStart()) || $select->getLimitStart() == 0) ? '0' : '1';
        $mask .= is_null($select->getLimitCount()) ? '0' : '1';
        $limit = '';

        if ($mask !== "00") {
            $start = $this->placeholderWriter->add($select->getLimitStart());
            $count = $this->placeholderWriter->add($select->getLimitCount());

            $limit = "LIMIT {$start}, {$count}";
        }

        return $limit;
    }
}
