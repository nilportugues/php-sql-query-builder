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
     * @param Table $table
     *
     * @return Select
     */
    public static function createSelect(Table $table = null)
    {
        $object = new Select();

        if (!is_null($table)) {
            $object->setTable($table);
        }

        return $object;
    }

    /**
     * @return Insert
     */
    public static function createInsert()
    {
        return new Insert();
    }

    /**
     * @return Update
     */
    public static function createUpdate()
    {
        return new Update();
    }

    /**
     * @return Delete
     */
    public static function createDelete()
    {
        return new Delete();
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
}
