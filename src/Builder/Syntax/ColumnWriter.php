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
            function (mixed &$columnData) use ($selectWriter): void {
                // $columnData is expected to be an array like ['alias_or_index' => Select_object_or_string]
                $keys = \array_keys($columnData);
                $originalKey = \array_pop($keys); // Get the alias or numeric index

                $values = \array_values($columnData);
                $content = $values[0]; // This is the Select object or column name string

                $aliasToUse = $originalKey; // Default to original key

                if (\is_numeric($originalKey) && $content instanceof \NilPortugues\Sql\QueryBuilder\Manipulation\Select) {
                    /** @var \NilPortugues\Sql\QueryBuilder\Syntax\Table|null $firstTable */
                    $firstTable = $content->getTable(); // Get the main table of the subquery

                    if ($firstTable) { // Check if a table is actually set
                        $derivedAlias = $firstTable->getAlias();
                        if (null === $derivedAlias || $derivedAlias === '') {
                            $derivedAlias = $firstTable->getName();
                        }

                        if ($derivedAlias && $derivedAlias !== '') {
                            $aliasToUse = $derivedAlias;
                        }
                    }
                    // If no table or no alias/name, $aliasToUse remains $originalKey (numeric string)
                }
                // Important: ensure $aliasToUse is a string for selectToColumn
                // The variable name for the modified element in array_walk is $columnData itself
                $columnData = $selectWriter->selectToColumn((string)$aliasToUse, $content);
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
