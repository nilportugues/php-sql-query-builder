<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/11/14
 * Time: 1:50 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;

/**
 * Class DeleteWriter.
 */
class DeleteWriter
{
    public function __construct(
        protected GenericBuilder $writer,
        protected PlaceholderWriter $placeholderWriter
    ) {
    }

    public function write(Delete $delete): string
    {
        $tableInstance = $delete->getTable();
        if ($tableInstance === null) {
            throw new QueryException("DELETE query must specify a table.");
        }
        $table = $this->writer->writeTable($tableInstance);
        /** @var array<string> $parts */
        $parts = ["DELETE FROM {$table}"];

        AbstractBaseWriter::writeWhereCondition($delete, $this->writer, $this->placeholderWriter, $parts);
        AbstractBaseWriter::writeLimitCondition($delete, $this->placeholderWriter, $parts);
        $comment = AbstractBaseWriter::writeQueryComment($delete);

        return $comment . implode(' ', $parts);
    }
}
