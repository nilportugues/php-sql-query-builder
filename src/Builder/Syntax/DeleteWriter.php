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
        $table = $this->writer->writeTable($delete->getTable());
        /** @var array<string> $parts */
        $parts = ["DELETE FROM {$table}"];

        AbstractBaseWriter::writeWhereCondition($delete, $this->writer, $this->placeholderWriter, $parts);
        AbstractBaseWriter::writeLimitCondition($delete, $this->placeholderWriter, $parts);
        $comment = AbstractBaseWriter::writeQueryComment($delete);

        return $comment . implode(' ', $parts);
    }
}
