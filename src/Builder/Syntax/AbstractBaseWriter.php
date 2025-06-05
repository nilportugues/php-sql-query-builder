<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/24/14
 * Time: 1:14 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\AbstractBaseQuery;

/**
 * Class AbstractBaseWriter.
 */
abstract class AbstractBaseWriter
{
    protected ColumnWriter $columnWriter;

    public function __construct(
        protected GenericBuilder $writer,
        protected PlaceholderWriter $placeholderWriter
    ) {
        $this->columnWriter = WriterFactory::createColumnWriter($this->writer, $this->placeholderWriter);
    }

    public static function writeQueryComment(AbstractBaseQuery $class): string
    {
        $comment = '';
        if ('' !== $class->getComment()) {
            $comment = $class->getComment();
        }

        return $comment;
    }

    /**
     * @param array<string> $parts
     */
    public static function writeWhereCondition(
        AbstractBaseQuery $class,
        GenericBuilder $writer,
        PlaceholderWriter $placeholderWriter,
        array &$parts
    ): void {
        if (null !== $class->getWhere()) {
            $whereWriter = WriterFactory::createWhereWriter($writer, $placeholderWriter);
            $parts[] = "WHERE {$whereWriter->writeWhere($class->getWhere())}";
        }
    }

    /**
     * @param array<string> $parts
     */
    public static function writeLimitCondition(
        AbstractBaseQuery $class,
        PlaceholderWriter $placeholderWriter,
        array &$parts
    ): void {
        if (null !== $class->getLimitStart()) {
            $start = $placeholderWriter->add($class->getLimitStart());
            $parts[] = "LIMIT {$start}";
        }
    }
}
