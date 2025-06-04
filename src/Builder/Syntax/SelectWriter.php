<?php

declare(strict_types=1);

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
// Removed duplicate imports that were causing fatal error
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use NilPortugues\Sql\QueryBuilder\Manipulation\Join; // Added

// use NilPortugues\Sql\QueryBuilder\Syntax\Having; // Replaced by Where

/**
 * Class SelectWriter.
 */
class SelectWriter extends AbstractBaseWriter
{
    public function selectToColumn(string $alias, Select $select): Column
    {
        $selectAsColumn = $this->write($select);

        if ($selectAsColumn !== '') {
            $selectAsColumn = '(' . $selectAsColumn . ')';
        }

        $column = [$alias => $selectAsColumn];

        return SyntaxFactory::createColumn($column, null);
    }

    public function write(Select $select): string
    {
        if ($select->isJoinSelect()) {
            // Assuming writeJoin is part of GenericBuilder and returns string
            return $this->writer->writeJoin($select);
        }

        return $this->writeSelectQuery($select);
    }

    protected function writeSelectQuery(Select $select): string
    {
        /** @var array<string> $parts */
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

        return AbstractBaseWriter::writeQueryComment($select) . implode(' ', \array_filter($parts));
    }

    /**
     * @param array<string> $parts
     */
    public function writeSelectColumns(Select $select, array &$parts): self
    {
        if ($select->isCount() === false) {
            $columnsToWrite = $this->writeColumnAlias(
                $select->getAllColumns(),
                $this->columnWriter->writeSelectsAsColumns($select),
                $this->columnWriter->writeValueAsColumns($select),
                $this->columnWriter->writeFuncAsColumns($select)
            );

            if (!empty($columnsToWrite)) {
                $parts[] = implode(', ', $columnsToWrite);
            }
            return $this;
        }

        $columns = $select->getColumns();
        /** @var Column|null $column */
        $column = \array_pop($columns);
        if ($column) {
            $columnList = $column->getName(); // Assuming getName() exists and returns string
            $parts[] = (string)$columnList;
        }

        return $this;
    }

    /**
     * @param array<Column> $tableColumns
     * @param array<Column|string> $selectAsColumns
     * @param array<Column> $valueAsColumns
     * @param array<Column> $funcAsColumns
     * @return array<string>
     */
    protected function writeColumnAlias(
        array $tableColumns,
        array $selectAsColumns,
        array $valueAsColumns,
        array $funcAsColumns
    ): array {
        /** @var array<Column> $mergedColumns */
        $mergedColumns = \array_merge($tableColumns, $selectAsColumns, $valueAsColumns, $funcAsColumns);
        $writtenColumns = [];
        foreach ($mergedColumns as $column) {
            if ($column instanceof Column) { // Ensure it's a column object
                $writtenColumns[] = $this->columnWriter->writeColumnWithAlias($column);
            }
        }
        return $writtenColumns;
    }

    /**
     * @param array<string> $parts
     */
    public function writeSelectFrom(Select $select, array &$parts): self
    {
        $tableRepresentation = $this->writer->writeTableWithAlias($select->getTable());
        if ($tableRepresentation !== '') {
            $parts[] = 'FROM';
            $parts[] = $tableRepresentation;
        }
        return $this;
    }

    /**
     * @param array<string> $parts
     */
    public function writeSelectJoins(Select $select, array &$parts): self
    {
        $joinsString = $this->writeSelectAggrupation(
            $select,
            $this->writer,
            'getAllJoins',
            'writeJoin',
            ' '
        );
        if ($joinsString !== '') {
            $parts[] = $joinsString;
        }
        return $this;
    }

    /**
     * @param GenericBuilder|ColumnWriter $writerObject
     * @param string $getMethod Name of the method on Select to get items (e.g., getAllJoins)
     * @param string $writeMethod Name of the method on $writerObject to write an item
     * @param string $glue Separator string
     * @param string $prepend String to prepend if items exist
     * @return string
     */
    protected function writeSelectAggrupation(
        Select $select,
        object $writerObject,
        string $getMethod,
        string $writeMethod,
        string $glue,
        string $prepend = ''
    ): string {
        $items = $select->$getMethod(); // e.g. $select->getAllJoins()
        $writtenItems = [];

        if (!empty($items)) {
            /** @var Join|Column|OrderBy|Where $item */ // Added Where for Having case
            foreach ($items as $item) {
                // e.g. $this->writer->writeJoin($item) or $this->columnWriter->writeColumn($item)
                $writtenItems[] = $writerObject->$writeMethod($item);
            }
            return $prepend . implode($glue, $writtenItems);
        }

        return '';
    }

    /**
     * @param array<string> $parts
     */
    public function writeSelectWhere(Select $select, array &$parts): self
    {
        $wheres = $this->writeSelectWheres($select->getAllWheres());
        $wheres = \array_filter($wheres); // Remove empty strings

        if (\count($wheres) > 0) {
            $parts[] = 'WHERE';
            $separator = ' ' . $this->writer->writeConjunction($select->getWhereOperator()) . ' ';
            $parts[] = \implode($separator, $wheres);
        }
        return $this;
    }

    /**
     * @param array<Where> $wheres
     * @return array<string>
     */
    protected function writeSelectWheres(array $wheres): array
    {
        $whereWriter = WriterFactory::createWhereWriter($this->writer, $this->placeholderWriter);
        $writtenWheres = [];
        foreach ($wheres as $where) {
            $writtenWheres[] = $whereWriter->writeWhere($where);
        }
        return $writtenWheres;
    }

    /**
     * @param array<string> $parts
     */
    public function writeSelectGroupBy(Select $select, array &$parts): self
    {
        $groupByString = $this->writeSelectAggrupation(
            $select,
            $this->columnWriter,
            'getGroupBy',
            'writeColumn',
            ', ',
            'GROUP BY '
        );
        if ($groupByString !== '') {
            $parts[] = $groupByString;
        }
        return $this;
    }

    /**
     * @param array<string> $parts
     */
    public function writeSelectHaving(Select $select, array &$parts): self
    {
        /** @var array<Where> $havingArray */ // Changed Having to Where
        $havingArray = $select->getAllHavings();

        if (\count($havingArray) > 0) {
            $writtenHavings = $this->getHavingConditions($havingArray, $select, $this->writer, $this->placeholderWriter);
            $writtenHavings = \array_filter($writtenHavings);

            if (!empty($writtenHavings)) {
                $parts[] = 'HAVING';
                $separator = ' ' . $this->writer->writeConjunction($select->getHavingOperator()) . ' '; // Use conjunction from GenericBuilder
                $parts[] = \implode($separator, $writtenHavings);
            }
        }
        return $this;
    }

    /**
     * @param array<Where> $havingArray  // Changed Having to Where
     * @return array<string>
     */
    protected function getHavingConditions(
        array $havingArray, // Pass by value, return new array
        Select $select,
        GenericBuilder $writer,
        PlaceholderWriter $placeholder
    ): array {
        $writtenConditions = [];
        $whereWriter = WriterFactory::createWhereWriter($writer, $placeholder);
        /** @var Where $having */ // Ensure $having is treated as Where
        foreach ($havingArray as $having) {
            $clauses = $whereWriter->writeWhereClauses($having);
            if (!empty($clauses)) {
                $writtenConditions[] = \implode($this->writer->writeConjunction($select->getHavingOperator()), $clauses);
            }
        }
        return $writtenConditions;
    }

    /**
     * @param array<string> $parts
     */
    protected function writeSelectOrderBy(Select $select, array &$parts): self
    {
        /** @var array<OrderBy> $orderByArray */
        $orderByArray = $select->getAllOrderBy();
        $writtenOrderBys = [];

        if (\count($orderByArray) > 0) {
            foreach ($orderByArray as $orderBy) {
                $writtenOrderBys[] = $this->writeOrderBy($orderBy);
            }
            $parts[] = 'ORDER BY';
            $parts[] = \implode(', ', $writtenOrderBys);
        }
        return $this;
    }

    public function writeOrderBy(OrderBy $orderBy): string
    {
        $column = $this->columnWriter->writeColumn($orderBy->getColumn());
        return $column . ' ' . $orderBy->getDirection();
    }

    /**
     * @param array<string> $parts
     */
    protected function writeSelectLimit(Select $select, array &$parts): self
    {
        $mask = $this->getStartingLimit($select) . $this->getLimitCount($select);

        if ($mask !== '00') {
            $start = $this->placeholderWriter->add($select->getLimitStart()); // Ensure getLimitStart() is not null
            $count = $this->placeholderWriter->add($select->getLimitCount()); // Ensure getLimitCount() is not null
            $parts[] = "LIMIT {$start}, {$count}";
        }
        return $this;
    }

    protected function getStartingLimit(Select $select): string
    {
        $limitStart = $select->getLimitStart();
        return ($limitStart === null || $limitStart === 0) ? '0' : '1';
    }

    protected function getLimitCount(Select $select): string
    {
        return ($select->getLimitCount() === null) ? '0' : '1';
    }
}
