<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Syntax;

/**
 * Class SyntaxFactory.
 */
final class SyntaxFactory
{
    /**
     * Creates a collection of Column objects.
     *
     * @param array      $arguments
     * @param Table|null $table
     *
     * @return array
     */
    public static function createColumns(array &$arguments, $table = null)
    {
        $createdColumns = [];

        foreach ($arguments as $index => $column) {
            if (!is_object($column)) {
                $newColumn = array($column);
                $column = self::createColumn($newColumn, $table);
                if (!is_numeric($index)) {
                    $column->setAlias($index);
                }

                $createdColumns[] = $column;
            } else if ($column instanceof Column) {
                $createdColumns[] = $column;
            }
        }

        return \array_filter($createdColumns);
    }

    /**
     * Creates a Column object.
     *
     * @param array      $argument
     * @param null|Table $table
     *
     * @return Column
     */
    public static function createColumn(array &$argument, $table = null)
    {
        $columnName = \array_values($argument);
        $columnName = $columnName[0];

        $columnAlias = \array_keys($argument);
        $columnAlias = $columnAlias[0];

        if (\is_numeric($columnAlias) || \strpos($columnName, '*') !== false) {
            $columnAlias = null;
        }

        return new Column($columnName, (string) $table, $columnAlias);
    }

    /**
     * Creates a Table object.
     *
     * @param string[] $table
     *
     * @return Table
     */
    public static function createTable($table)
    {
        $tableName = $table;
        if (\is_array($table)) {
            $tableName = \current($table);
            $tableAlias = \key($table);
        }

        $newTable = new Table($tableName);

        if (isset($tableAlias) && !is_numeric($tableAlias)) {
            $newTable->setAlias($tableAlias);
        }

        return $newTable;
    }
}
