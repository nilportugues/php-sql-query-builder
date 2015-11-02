<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/11/14
 * Time: 1:50 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;

/**
 * Class SelectWriter.
 */
class SelectWriter extends AbstractBaseWriter
{
    /**
     * @param        $alias
     * @param Select $select
     *
     * @return Column
     */
    public function selectToColumn($alias, Select $select)
    {
        $selectAsColumn = $this->write($select);

        if (!empty($selectAsColumn)) {
            $selectAsColumn = '('.$selectAsColumn.')';
        }

        $column = array($alias => $selectAsColumn);

        return SyntaxFactory::createColumn($column, null);
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function write(Select $select)
    {
        if ($select->isJoinSelect()) {
            return $this->writer->writeJoin($select);
        }

        return $this->writeSelectQuery($select);
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    protected function writeSelectQuery(Select $select)
    {
        $parts = ['SELECT'];

        if ($select->isDistinct()) {
            $parts[] = 'DISTINCT';
        }

        $this->writeSelectColumns($select, $parts);
        $this->writeSelectFrom($select, $parts);
        $this->writeSelectJoins($select, $parts);
        $this->writeSelectWhere($select, $parts);
        $this->writeSelectGroupBy($select, $parts);
        $this->writeSelectHaving($select, $parts);
        $this->writeSelectOrderBy($select, $parts);
        $this->writeSelectLimit($select, $parts);

        return AbstractBaseWriter::writeQueryComment($select).implode(' ', \array_filter($parts));
    }

    /**
     * @param Select   $select
     * @param string[] $parts
     *
     * @return $this
     */
    public function writeSelectColumns(Select $select, array &$parts)
    {
        if ($select->isCount() === false) {
            $columns = $this->writeColumnAlias(
                $select->getAllColumns(),
                $this->columnWriter->writeSelectsAsColumns($select),
                $this->columnWriter->writeValueAsColumns($select),
                $this->columnWriter->writeFuncAsColumns($select)
            );

            $parts = \array_merge($parts, [implode(', ', $columns)]);

            return $this;
        }

        $columns = $select->getColumns();
        $column = \array_pop($columns);
        $columnList = $column->getName();

        $parts = \array_merge($parts, [$columnList]);

        return $this;
    }

    /**
     * @param $tableColumns
     * @param $selectAsColumns
     * @param $valueAsColumns
     * @param $funcAsColumns
     *
     * @return array
     */
    protected function writeColumnAlias($tableColumns, $selectAsColumns, $valueAsColumns, $funcAsColumns)
    {
        $columns = \array_merge($tableColumns, $selectAsColumns, $valueAsColumns, $funcAsColumns);

        \array_walk(
            $columns,
            function (&$column) {
                $column = $this->columnWriter->writeColumnWithAlias($column);
            }
        );

        return $columns;
    }

    /**
     * @param Select   $select
     * @param string[] $parts
     *
     * @return $this
     */
    public function writeSelectFrom(Select $select, array &$parts)
    {
        $parts = \array_merge(
            $parts,
            ['FROM '.$this->writer->writeTableWithAlias($select->getTable())]
        );

        return $this;
    }

    /**
     * @param Select $select
     * @param array  $parts
     *
     * @return $this
     */
    public function writeSelectJoins(Select $select, array &$parts)
    {
        $parts = \array_merge(
            $parts,
            [$this->writeSelectAggrupation($select, $this->writer, 'getAllJoins', 'writeJoin', ' ')]
        );

        return $this;
    }

    /**
     * @param Select $select
     * @param        $writer
     * @param string $getMethod
     * @param string $writeMethod
     * @param string $glue
     * @param string $prepend
     *
     * @return string
     */
    protected function writeSelectAggrupation(Select $select, $writer, $getMethod, $writeMethod, $glue, $prepend = '')
    {
        $str = '';
        $joins = $select->$getMethod();

        if (!empty($joins)) {
            \array_walk(
                $joins,
                function (&$join) use ($writer, $writeMethod) {
                    $join = $writer->$writeMethod($join);
                }
            );

            $str = $prepend.implode($glue, $joins);
        }

        return $str;
    }

    /**
     * @param Select $select
     * @param array  $parts
     *
     * @return $this
     */
    public function writeSelectWhere(Select $select, array &$parts)
    {
        $str = '';
        $wheres = $this->writeSelectWheres($select->getAllWheres());
        $wheres = \array_filter($wheres);

        if (\count($wheres) > 0) {
            $str = 'WHERE ';
            $separator = ' '.$this->writer->writeConjunction($select->getWhereOperator()).' ';

            $str .= \implode($separator, $wheres);
        }

        $parts = \array_merge($parts, [$str]);

        return $this;
    }

    /**
     * @param array $wheres
     *
     * @return array
     */
    protected function writeSelectWheres(array $wheres)
    {
        $whereWriter = WriterFactory::createWhereWriter($this->writer, $this->placeholderWriter);

        \array_walk(
            $wheres,
            function (&$where) use (&$whereWriter) {

                $where = $whereWriter->writeWhere($where);
            }
        );

        return $wheres;
    }

    /**
     * @param Select $select
     * @param array  $parts
     *
     * @return $this
     */
    public function writeSelectGroupBy(Select $select, array &$parts)
    {
        $groupBy = $this->writeSelectAggrupation(
            $select,
            $this->columnWriter,
            'getGroupBy',
            'writeColumn',
            ', ',
            'GROUP BY '
        );

        $parts = \array_merge($parts, [$groupBy]);

        return $this;
    }

    /**
     * @param Select $select
     * @param array  $parts
     *
     * @return $this
     */
    public function writeSelectHaving(Select $select, array &$parts)
    {
        $str = '';
        $havingArray = $select->getAllHavings();

        if (\count($havingArray) > 0) {
            $placeholder = $this->placeholderWriter;
            $writer = $this->writer;

            $str = 'HAVING ';
            $separator = ' '.$select->getHavingOperator().' ';
            $havingArray = $this->getHavingConditions($havingArray, $select, $writer, $placeholder);

            $str .= \implode($separator, $havingArray);
        }

        $parts = \array_merge($parts, [$str]);

        return $this;
    }

    /**
     * @param array             $havingArray
     * @param Select            $select
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholder
     *
     * @return mixed
     */
    protected function getHavingConditions(
        array &$havingArray,
        Select $select,
        GenericBuilder $writer,
        PlaceholderWriter $placeholder
    ) {
        \array_walk(
            $havingArray,
            function (&$having) use ($select, $writer, $placeholder) {

                $whereWriter = WriterFactory::createWhereWriter($writer, $placeholder);
                $clauses = $whereWriter->writeWhereClauses($having);
                $having = \implode($this->writer->writeConjunction($select->getHavingOperator()), $clauses);
            }
        );

        return $havingArray;
    }

    /**
     * @param Select $select
     * @param array  $parts
     *
     * @return $this
     */
    protected function writeSelectOrderBy(Select $select, array &$parts)
    {
        $str = '';
        if (\count($select->getAllOrderBy())) {
            $orderByArray = $select->getAllOrderBy();
            \array_walk(
                $orderByArray,
                function (&$orderBy) {
                    $orderBy = $this->writeOrderBy($orderBy);
                }
            );

            $str = 'ORDER BY ';
            $str .= \implode(', ', $orderByArray);
        }

        $parts = \array_merge($parts, [$str]);

        return $this;
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
     * @param array  $parts
     *
     * @return $this
     */
    protected function writeSelectLimit(Select $select, array &$parts)
    {
        $mask = $this->getStartingLimit($select).$this->getLimitCount($select);

        $limit = '';

        if ($mask !== '00') {
            $start = $this->placeholderWriter->add($select->getLimitStart());
            $count = $this->placeholderWriter->add($select->getLimitCount());

            $limit = "LIMIT {$start}, {$count}";
        }

        $parts = \array_merge($parts, [$limit]);

        return $this;
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    protected function getStartingLimit(Select $select)
    {
        return (null === $select->getLimitStart() || 0 == $select->getLimitStart()) ? '0' : '1';
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    protected function getLimitCount(Select $select)
    {
        return (null === $select->getLimitCount()) ? '0' : '1';
    }
}
