<?php
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
    /**
     * @var GenericBuilder
     */
    protected $writer;

    /**
     * @var PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * @var ColumnWriter
     */
    protected $columnWriter;

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(GenericBuilder $writer, PlaceholderWriter $placeholder)
    {
        $this->writer = $writer;
        $this->placeholderWriter = $placeholder;

        $this->columnWriter = WriterFactory::createColumnWriter($writer, $placeholder);
    }

    /**
     * @param AbstractBaseQuery $class
     *
     * @return string
     */
    public static function writeQueryComment(AbstractBaseQuery $class)
    {
        $comment = '';
        if ('' !== $class->getComment()) {
            $comment = $class->getComment();
        }

        return $comment;
    }

    /**
     * @param AbstractBaseQuery $class
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholderWriter
     * @param array             $parts
     */
    public static function writeWhereCondition(
        AbstractBaseQuery $class,
        $writer, PlaceholderWriter
        $placeholderWriter,
        array &$parts
    ) {
        if (!is_null($class->getWhere())) {
            $whereWriter = WriterFactory::createWhereWriter($writer, $placeholderWriter);
            $parts[] = "WHERE {$whereWriter->writeWhere($class->getWhere())}";
        }
    }

    /**
     * @param AbstractBaseQuery $class
     * @param PlaceholderWriter $placeholderWriter
     * @param array             $parts
     */
    public static function writeLimitCondition(
        AbstractBaseQuery $class,
        PlaceholderWriter $placeholderWriter,
        array &$parts
    ) {
        if (!is_null($class->getLimitStart())) {
            $start = $placeholderWriter->add($class->getLimitStart());
            $parts[] = "LIMIT {$start}";
        }
    }
}
