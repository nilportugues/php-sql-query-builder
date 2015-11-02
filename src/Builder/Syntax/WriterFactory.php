<?php
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
    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return ColumnWriter
     */
    public static function createColumnWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        return new ColumnWriter($writer, $placeholderWriter);
    }

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return WhereWriter
     */
    public static function createWhereWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        return new WhereWriter($writer, $placeholderWriter);
    }

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return SelectWriter
     */
    public static function createSelectWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        return new SelectWriter($writer, $placeholderWriter);
    }

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return InsertWriter
     */
    public static function createInsertWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        return new InsertWriter($writer, $placeholderWriter);
    }

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return UpdateWriter
     */
    public static function createUpdateWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        return new UpdateWriter($writer, $placeholderWriter);
    }

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     *
     * @return DeleteWriter
     */
    public static function createDeleteWriter(GenericBuilder $writer, PlaceholderWriter $placeholderWriter)
    {
        return new DeleteWriter($writer, $placeholderWriter);
    }

    /**
     * @return PlaceholderWriter
     */
    public static function createPlaceholderWriter()
    {
        return new PlaceholderWriter();
    }

    /**
     * @param GenericBuilder $writer
     *
     * @return IntersectWriter
     */
    public static function createIntersectWriter(GenericBuilder $writer)
    {
        return new IntersectWriter($writer);
    }

    /**
     * @param GenericBuilder $writer
     *
     * @return MinusWriter
     */
    public static function createMinusWriter(GenericBuilder $writer)
    {
        return new MinusWriter($writer);
    }

    /**
     * @param GenericBuilder $writer
     *
     * @return UnionWriter
     */
    public static function createUnionWriter(GenericBuilder $writer)
    {
        return new UnionWriter($writer);
    }

    /**
     * @param GenericBuilder $writer
     *
     * @return UnionAllWriter
     */
    public static function createUnionAllWriter(GenericBuilder $writer)
    {
        return new UnionAllWriter($writer);
    }
}
