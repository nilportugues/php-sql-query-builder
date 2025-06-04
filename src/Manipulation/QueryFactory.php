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

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class QueryFactory.
 */
final class QueryFactory
{
    /**
     * @param array<string>|null $columns
     */
    public static function createSelect(?string $table = null, ?array $columns = null): Select
    {
        return new Select($table, $columns);
    }

    /**
     * @param array<mixed>|null $values
     */
    public static function createInsert(?string $table = null, ?array $values = null): Insert
    {
        return new Insert($table, $values);
    }

    /**
     * @param array<mixed>|null $values
     */
    public static function createUpdate(?string $table = null, ?array $values = null): Update
    {
        return new Update($table, $values);
    }

    public static function createDelete(?string $table = null): Delete
    {
        return new Delete($table);
    }

    public static function createWhere(QueryInterface $query): Where
    {
        return new Where($query);
    }

    public static function createIntersect(): Intersect
    {
        return new Intersect();
    }

    public static function createMinus(Select $first, Select $second): Minus
    {
        return new Minus($first, $second);
    }

    public static function createUnion(): Union
    {
        return new Union();
    }

    public static function createUnionAll(): UnionAll
    {
        return new UnionAll();
    }
}
