<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/12/14
 * Time: 1:28 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;

/**
 * Class ColumnWriter.
 */
class ColumnWriter
{
    public function __construct(
        protected GenericBuilder $writer,
        protected PlaceholderWriter $placeholderWriter
    ) {
    }

    /**
     * @return array<string|Column>
     */
    public function writeSelectsAsColumns(Select $select): array
    {
        $selectAsColumns = $select->getColumnSelects();

        if (!empty($selectAsColumns)) {
            $selectWriter = WriterFactory::createSelectWriter($this->writer, $this->placeholderWriter);
            $selectAsColumns = $this->selectColumnToQuery($selectAsColumns, $selectWriter);
        }

        return $selectAsColumns;
    }

    /**
     * @param array<string|Column> $selectAsColumns
     * @return array<string|Column>
     */
    protected function selectColumnToQuery(array &$selectAsColumns, SelectWriter $selectWriter): array
    {
        \array_walk(
            $selectAsColumns,
            function (mixed &$column) use ($selectWriter): void {
                $keys = \array_keys($column);
                $key = \array_pop($keys);

                $values = \array_values($column);
                /** @var Column|string $value */
                $value = $values[0];

                if (\is_numeric($key) && $value instanceof Column) {
                    $key = $this->writer->writeTableName($value->getTable());
                }
                $column = $selectWriter->selectToColumn((string)$key, $value);
            }
        );

        return $selectAsColumns;
    }

    /**
     * @return array<Column>
     */
    public function writeValueAsColumns(Select $select): array
    {
        $valueAsColumns = $select->getColumnValues();
        $newColumns = [];

        if (!empty($valueAsColumns)) {
            foreach ($valueAsColumns as $alias => $value) {
                /** @var string $alias */
                $writtenValue = $this->writer->writePlaceholderValue($value);
                $newValueColumn = [$alias => $writtenValue];

                $newColumns[] = SyntaxFactory::createColumn($newValueColumn, null);
            }
        }

        return $newColumns;
    }

    /**
     * @return array<Column>
     */
    public function writeFuncAsColumns(Select $select): array
    {
        $funcAsColumns = $select->getColumnFuncs();
        $newColumns = [];

        if (!empty($funcAsColumns)) {
            foreach ($funcAsColumns as $alias => $valueDetails) {
                /** @var string $alias */
                /** @var array{func: string, args: string[]} $valueDetails */
                $funcName = $valueDetails['func'];
                $funcArgs = !empty($valueDetails['args']) ? '('.implode(', ', $valueDetails['args']).')' : '';

                $newFuncColumn = [$alias => $funcName.$funcArgs];
                $newColumns[] = SyntaxFactory::createColumn($newFuncColumn, null);
            }
        }

        return $newColumns;
    }

    public function writeColumnWithAlias(Column $column): string
    {
        $alias = $column->getAlias();
        if ($alias !== null && $alias !== '' && !$column->isAll()) {
            return $this->writeColumn($column).' AS '.$this->writer->writeColumnAlias($alias);
        }

        return $this->writeColumn($column);
    }

    public function writeColumn(Column $column): string
    {
        $tableInstance = $column->getTable();
        $alias = $tableInstance?->getAlias(); // Potentially null if table is not set for a column
        $table = '';

        if ($tableInstance) {
            $table = ($alias) ? $this->writer->writeTableAlias($alias) : $this->writer->writeTable($tableInstance);
        }

        $columnString = ($table === '') ? '' : "{$table}.";
        $columnString .= $this->writer->writeColumnName($column);

        return $columnString;
    }
}
