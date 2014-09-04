<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/12/14
 * Time: 1:28 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Select;
use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\QueryPartInterface;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;

/**
 * Class ColumnWriter
 * @package NilPortugues\SqlQueryBuilder\BuilderInterface\Syntax
 */
class ColumnWriter
{
    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     */
    public function __construct(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        $this->writer            = $writer;
        $this->placeholderWriter = $placeholderWriter;
    }

    /**
     * @param QueryPartInterface $column
     *
     * @return string
     */
    public function writeColumn(QueryPartInterface $column)
    {
        $alias = $column->getTable()->getAlias();
        $table = ($alias) ? $this->writer->writeAlias($alias) : $this->writer->writeTable($column->getTable());

        $columnString = (empty($table)) ? '' : "{$table}.";
        $columnString .= $this->writer->writeColumnName($column);

        return $columnString;
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

            array_walk(
                $selectAsColumns,
                function (&$column) use (&$selectWriter) {
                    $key = array_keys($column);
                    $key = array_pop($key);

                    $values = array_values($column);
                    $value  = $values[0];

                    if (is_numeric($key)) {
                        $key = $this->writer->writeTableName($value->getTable());
                    }

                    $column = $selectWriter->selectToColumn($key, $value);
                }
            );
        }

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
        $newColumns     = array();

        if (!empty($valueAsColumns)) {

            foreach ($valueAsColumns as $alias => $value) {
                $value          = $this->writer->writePlaceholderValue($value);
                $newValueColumn = array($this->writer->writeAlias($alias) => $value);

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
        $newColumns    = array();

        if (!empty($funcAsColumns)) {

            foreach ($funcAsColumns as $alias => $value) {

                $funcName = $value['func'];
                $funcArgs = (!empty($value['args'])) ? "(".implode(', ', $value['args']).")" : '';

                $newFuncColumn = array($this->writer->writeAlias($alias) => $funcName.$funcArgs);
                $newColumns[]  = SyntaxFactory::createColumn($newFuncColumn, null);
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
            return $this->writeColumn($column)." AS ".$this->writer->writeAlias($alias);
        }

        return $this->writeColumn($column);
    }

}
