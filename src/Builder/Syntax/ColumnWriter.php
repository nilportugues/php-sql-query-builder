<?php
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
    /**
     * @var \NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder
     */
    protected $writer;

    /**
     * @var PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     */
    public function __construct(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        $this->writer = $writer;
        $this->placeholderWriter = $placeholderWriter;
    }

    /**
     * @param Select $select
     *
     * @return array
     */
    public function writeSelectsAsColumns(Select $select)
    {
        $selectAsColumns = $select->getColumnSelects();

        if (!empty($selectAsColumns)) {
            $selectWriter = WriterFactory::createSelectWriter($this->writer, $this->placeholderWriter);
            $selectAsColumns = $this->selectColumnToQuery($selectAsColumns, $selectWriter);
        }

        return $selectAsColumns;
    }

    /**
     * @param array        $selectAsColumns
     * @param SelectWriter $selectWriter
     *
     * @return mixed
     */
    protected function selectColumnToQuery(array &$selectAsColumns, SelectWriter $selectWriter)
    {
        \array_walk(
            $selectAsColumns,
            function (&$column) use (&$selectWriter) {
                $keys = \array_keys($column);
                $key = \array_pop($keys);

                $values = \array_values($column);
                $value = $values[0];

                if (\is_numeric($key)) {
                    /* @var Column $value */
                    $key = $this->writer->writeTableName($value->getTable());
                }
                $column = $selectWriter->selectToColumn($key, $value);
            }
        );

        return $selectAsColumns;
    }

    /**
     * @param Select $select
     *
     * @return array
     */
    public function writeValueAsColumns(Select $select)
    {
        $valueAsColumns = $select->getColumnValues();
        $newColumns = [];

        if (!empty($valueAsColumns)) {
            foreach ($valueAsColumns as $alias => $value) {
                $value = $this->writer->writePlaceholderValue($value);
                $newValueColumn = array($alias => $value);

                $newColumns[] = SyntaxFactory::createColumn($newValueColumn, null);
            }
        }

        return $newColumns;
    }

    /**
     * @param Select $select
     *
     * @return array
     */
    public function writeFuncAsColumns(Select $select)
    {
        $funcAsColumns = $select->getColumnFuncs();
        $newColumns = [];

        if (!empty($funcAsColumns)) {
            foreach ($funcAsColumns as $alias => $value) {
                $funcName = $value['func'];
                $funcArgs = (!empty($value['args'])) ? '('.implode(', ', $value['args']).')' : '';

                $newFuncColumn = array($alias => $funcName.$funcArgs);
                $newColumns[] = SyntaxFactory::createColumn($newFuncColumn, null);
            }
        }

        return $newColumns;
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function writeColumnWithAlias(Column $column)
    {
        if (($alias = $column->getAlias()) && !$column->isAll()) {
            return $this->writeColumn($column).' AS '.$this->writer->writeColumnAlias($alias);
        }

        return $this->writeColumn($column);
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function writeColumn(Column $column)
    {
        $alias = $column->getTable()->getAlias();
        $table = ($alias) ? $this->writer->writeTableAlias($alias) : $this->writer->writeTable($column->getTable());

        $columnString = (empty($table)) ? '' : "{$table}.";
        $columnString .= $this->writer->writeColumnName($column);

        return $columnString;
    }
}
