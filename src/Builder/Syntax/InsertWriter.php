<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/11/14
 * Time: 1:51 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\Insert;
use NilPortugues\SqlQueryBuilder\Manipulation\QueryException;

/**
 * Class InsertWriter
 * @package NilPortugues\SqlQueryBuilder\BuilderInterface\Syntax
 */
class InsertWriter
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var ColumnWriter
     */
    private $columnWriter;

    /**
     * @param GenericBuilder    $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(GenericBuilder $writer, PlaceholderWriter $placeholder)
    {
        $this->writer       = $writer;
        $this->columnWriter = WriterFactory::createColumnWriter($this->writer, $placeholder);
    }

    /**
     * @param Insert $insert
     *
     * @throws QueryException
     * @return string
     */
    public function write(Insert $insert)
    {
        $columns = $insert->getColumns();
        $values  = $insert->getValues();

        if (empty($columns)) {
            throw new QueryException('No columns were defined for the current schema.');
        }

        array_walk(
            $columns,
            function (&$column) {
                $column = $this->columnWriter->writeColumn($column);
            }
        );

        array_walk(
            $values,
            function (&$value) {
                $value = $this->writer->writePlaceholderValue($value);
            }
        );

        $columns = implode(", ", $columns);
        $values  = implode(", ", $values);
        $table   = $this->writer->writeTable($insert->getTable());

        $comment = '';
        if ('' !== $insert->getComment()) {
            $comment = $insert->getComment();
        }

        return $comment."INSERT INTO {$table} ($columns) VALUES ($values)";
    }
}
