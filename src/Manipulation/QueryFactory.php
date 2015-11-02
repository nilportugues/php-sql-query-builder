<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class QueryFactory.
 */
final class QueryFactory
{
    /**
     * @param string $table
     * @param array  $columns
     *
     * @return Select
     */
    public static function createSelect($table = null, array $columns = null)
    {
        return new Select($table, $columns);
    }

    /**
     * @param string $table
     * @param array  $values
     *
     * @return Insert
     */
    public static function createInsert($table = null, array $values = null)
    {
        return new Insert($table, $values);
    }

    /**
     * @param string $table
     * @param array  $values
     *
     * @return Update
     */
    public static function createUpdate($table = null, array $values = null)
    {
        return new Update($table, $values);
    }

    /**
     * @param string $table
     *
     * @return Delete
     */
    public static function createDelete($table = null)
    {
        return new Delete($table);
    }

    /**
     * @param QueryInterface $query
     *
     * @return Where
     */
    public static function createWhere(QueryInterface $query)
    {
        return new Where($query);
    }

    /**
     * @return Intersect
     */
    public static function createIntersect()
    {
        return new Intersect();
    }

    /**
     * @param Select $first
     * @param Select $second
     *
     * @return Minus
     */
    public static function createMinus(Select $first, Select $second)
    {
        return new Minus($first, $second);
    }

    /**
     * @return Union
     */
    public static function createUnion()
    {
        return new Union();
    }

    /**
     * @return UnionAll
     */
    public static function createUnionAll()
    {
        return new UnionAll();
    }
}
