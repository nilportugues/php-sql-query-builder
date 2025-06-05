<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/12/14
 * Time: 2:11 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

/**
 * Class WriterFactory.
 */
final class WriterFactory
{
    public static function createColumnWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter): ColumnWriter
    {
        return new ColumnWriter($writer, $placeholderWriter);
    }

    public static function createWhereWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter): WhereWriter
    {
        return new WhereWriter($writer, $placeholderWriter);
    }

    public static function createSelectWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter): SelectWriter
    {
        return new SelectWriter($writer, $placeholderWriter);
    }

    public static function createInsertWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter): InsertWriter
    {
        return new InsertWriter($writer, $placeholderWriter);
    }

    public static function createUpdateWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter): UpdateWriter
    {
        return new UpdateWriter($writer, $placeholderWriter);
    }

    public static function createDeleteWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter): DeleteWriter
    {
        return new DeleteWriter($writer, $placeholderWriter);
    }

    public static function createPlaceholderWriter(): PlaceholderWriter
    {
        return new PlaceholderWriter();
    }

    public static function createIntersectWriter(GenericBuilder $writer): IntersectWriter
    {
        return new IntersectWriter($writer);
    }

    public static function createMinusWriter(GenericBuilder $writer): MinusWriter
    {
        return new MinusWriter($writer);
    }

    public static function createUnionWriter(GenericBuilder $writer): UnionWriter
    {
        return new UnionWriter($writer);
    }

    public static function createUnionAllWriter(GenericBuilder $writer): UnionAllWriter
    {
        return new UnionAllWriter($writer);
    }
}
