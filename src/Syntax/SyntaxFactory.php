<?php

declare(strict_types=1);

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
     * @param array<int|string, string|Column> $arguments Elements can be column name strings or Column objects.
     *                                                If string, key can be alias.
     * @return array<Column>
     */
    public static function createColumns(array $arguments, ?Table $table = null): array
    {
        $createdColumns = [];

        /** @var string|Column $columnData */
        foreach ($arguments as $indexOrAlias => $columnData) {
            if ($columnData instanceof Column) {
                $createdColumns[] = $columnData;
            } elseif (\is_string($columnData)) {
                // If $indexOrAlias is string, it's an alias. If numeric, no alias from index.
                // $columnData is the column name.
                $columnArray = [\is_string($indexOrAlias) ? $indexOrAlias : 0 => $columnData];
                $column = self::createColumn($columnArray, $table);
                $createdColumns[] = $column;
            }
        }
        // array_filter might not be necessary if logic ensures only Column objects are added.
        return $createdColumns;
    }

    /**
     * Creates a Column object.
     * Expects an array like ['alias' => 'column_name'] or [0 => 'column_name'].
     *
     * @param array<string|int, string> $argument
     */
    public static function createColumn(array $argument, ?Table $table = null): Column
    {
        $columnName = (string) \current($argument); // First value is name
        $columnAliasKey = \key($argument); // Key of the first element

        $alias = null;
    if (\is_string($columnAliasKey)) {
            $alias = $columnAliasKey;
        }

        // Column constructor expects table name as string, not Table object.
        // Table name can be null if column is not from a specific table (e.g. functions, literals)
        $tableNameString = $table?->getName();

        return new Column($columnName, $tableNameString, $alias ?? '');
    }

    /**
     * Creates a Table object.
     *
     * @param string|array<string|int, string> $tableInput Table name as string, or an array like ['alias' => 'tableName'] or ['tableName']
     */
    public static function createTable(string|array $tableInput): Table
    {
        $tableName = '';
        $tableAlias = null;

        if (\is_array($tableInput)) {
            $tableName = (string) \current($tableInput); // First value is name
            $aliasKey = \key($tableInput); // Key of the first element
            if (\is_string($aliasKey)) {
                $tableAlias = $aliasKey;
            }
        } else { // string
            $tableName = $tableInput;
        }

        $newTable = new Table($tableName);

        if ($tableAlias !== null) {
            $newTable->setAlias($tableAlias);
        }

        return $newTable;
    }
}
