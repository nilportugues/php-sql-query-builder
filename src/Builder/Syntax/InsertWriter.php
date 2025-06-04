<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/11/14
 * Time: 1:51 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
/**
 * Class InsertWriter.
 */
use NilPortugues\Sql\QueryBuilder\Syntax\Column;

class InsertWriter
{
    protected ColumnWriter $columnWriter;

    public function __construct(
        protected GenericBuilder $writer,
        PlaceholderWriter $placeholderWriter
    ) {
        $this->columnWriter = WriterFactory::createColumnWriter($this->writer, $placeholderWriter);
    }

    /**
     * @throws QueryException
     */
    public function write(Insert $insert): string
    {
        $columns = $insert->getColumns();

        if (empty($columns)) {
            throw new QueryException('No columns were defined for the current schema.');
        }

        $columnString = $this->writeQueryColumns($columns);
        $valueString = $this->writeQueryValues($insert->getValues());
        $table = $this->writer->writeTable($insert->getTable());
        $comment = AbstractBaseWriter::writeQueryComment($insert);

        return $comment . "INSERT INTO {$table} ({$columnString}) VALUES ({$valueString})";
    }

    /**
     * @param array<Column> $columns
     */
    protected function writeQueryColumns(array $columns): string
    {
        return $this->writeCommaSeparatedValues($columns, $this->columnWriter, 'writeColumn');
    }

    /**
     * @param array<mixed> $collection
     * @param ColumnWriter|GenericBuilder $writerObject
     */
    protected function writeCommaSeparatedValues(array $collection, object $writerObject, string $method): string
    {
        \array_walk(
            $collection,
            function (mixed &$data) use ($writerObject, $method): void {
                /** @var mixed $data */
                $data = $writerObject->$method($data);
            }
        );

        return \implode(', ', $collection);
    }

    /**
     * @param array<mixed> $values
     */
    protected function writeQueryValues(array $values): string
    {
        return $this->writeCommaSeparatedValues($values, $this->writer, 'writePlaceholderValue');
    }
}
