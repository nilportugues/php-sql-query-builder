<?php
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
    /**
     * {@inheritdoc}
     *
     * @param Column $column
     *
     * @return string
     */
    public function writeColumnName(Column $column)
    {
        if ($column->isAll()) {
            return '*';
        }

        if (false !== strpos($column->getName(), '(')) {
            return parent::writeColumnName($column);
        }

        return $this->wrapper(parent::writeColumnName($column));
    }

    /**
     * {@inheritdoc}
     *
     * @param Table $table
     *
     * @return string
     */
    public function writeTableName(Table $table)
    {
        return $this->wrapper(parent::writeTableName($table));
    }

    /**
     * {@inheritdoc}
     *
     * @param $alias
     *
     * @return string
     */
    public function writeTableAlias($alias)
    {
        return $this->wrapper(parent::writeTableAlias($alias));
    }

    /**
     * {@inheritdoc}
     *
     * @param $alias
     *
     * @return string
     */
    public function writeColumnAlias($alias)
    {
        return $this->wrapper($alias);
    }

    /**
     * @param        $string
     * @param string $char
     *
     * @return string
     */
    protected function wrapper($string, $char = '`')
    {
        if (0 === strlen($string)) {
            return '';
        }

        return $char.$string.$char;
    }
}
