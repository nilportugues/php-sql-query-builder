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

namespace NilPortugues\Sql\QueryBuilder\Builder;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;

/**
 * Class MySqlBuilder.
 */
class MySqlBuilder extends GenericBuilder
{
    public function writeColumnName(Column $column): string
    {
        if ($column->isAll()) {
            return '*';
        }

        // If column name contains '(', it's likely a function call, so don't wrap it.
        if (str_contains($column->getName(), '(')) {
            return parent::writeColumnName($column);
        }

        return $this->wrapper(parent::writeColumnName($column));
    }

    public function writeTableName(Table $table): string
    {
        return $this->wrapper(parent::writeTableName($table));
    }

    public function writeTableAlias(string $alias): string
    {
        return $this->wrapper(parent::writeTableAlias($alias));
    }

    public function writeColumnAlias(string $alias): string
    {
        // MySQL uses backticks for aliases if they need quoting,
        // but standard SQL uses double quotes.
        // Parent GenericBuilder uses double quotes.
        // This override will wrap with backticks.
        return $this->wrapper($alias);
    }

    protected function wrapper(string $string, string $char = '`'): string
    {
        if ($string === '') {
            return '';
        }
        // Avoid double wrapping if already wrapped by any common quote char
        if (isset($string[0]) && ($string[0] === '`' || $string[0] === '"' || $string[0] === "'")) {
            // Check if the string is already wrapped, if so, return as is or unwrap and re-wrap.
            // For simplicity here, if it starts with a quote, assume it's correctly quoted.
            // A more robust solution might involve checking if it's wrapped with the *same* $char.
            if (str_ends_with($string, $string[0])) {
                return $string;
            }
        }


        return $char . str_replace($char, $char.$char, $string) . $char;
    }
}
