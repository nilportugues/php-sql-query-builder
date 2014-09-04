<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\SqlQueryBuilder\Builder;

use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\Table;

/**
 * Class MySqlBuilder
 * @package NilPortugues\SqlQueryBuilder\Renderer
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
    public function writeAlias($alias)
    {
        return $this->wrapper(parent::writeAlias($alias));
    }

    /**
     * @param        $string
     * @param string $char
     *
     * @return string
     */
    protected function wrapper($string, $char = '`')
    {
        return $char.$string.$char;
    }
}
