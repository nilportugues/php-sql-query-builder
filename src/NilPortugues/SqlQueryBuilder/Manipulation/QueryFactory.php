<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Syntax\Table;
use NilPortugues\SqlQueryBuilder\Syntax\Where;

/**
 * Class QueryFactory
 * @package NilPortugues\SqlQueryBuilder\Manipulation
 */
final class QueryFactory
{
    /**
     * @param string $table
     *
     * @return Select
     */
    public static function createSelect($table = null)
    {
        return static::addTableToQuery(new Select(), $table);
    }

    /**
     * @param string $table
     *
     * @return Insert
     */
    public static function createInsert($table = null)
    {
        return static::addTableToQuery(new Insert(), $table);
    }

    /**
     * @param string $table
     *
     * @return Update
     */
    public static function createUpdate($table = null)
    {
        return static::addTableToQuery(new Update(), $table);
    }

    /**
     * @param string $table
     *
     * @return Delete
     */
    public static function createDelete($table = null)
    {
        return static::addTableToQuery(new Delete(), $table);
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

    /**
     * @param  NilPortugues\SqlQueryBuilder\Manipulation\BaseQuery $query
     * @param  string $table
     *
     * @return NilPortugues\SqlQueryBuilder\Manipulation\BaseQuery
     */
    private static function addTableToQuery(BaseQuery $query, $table = null)
    {
        if ($table) {
            $query->setTable($table);
        }

        return $query;
    }
}
