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

use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;

/**
 * Class UpdateWriter.
 */
class UpdateWriter extends AbstractBaseWriter
{
    /**
     * @throws QueryException
     */
    public function write(Update $update): string
    {
        $values = $update->getValues();
        if (empty($values)) {
            throw new QueryException('No values to update in Update query.');
        }

        /** @var array<string> $parts */
        $parts = [
            'UPDATE ' . $this->writer->writeTable($update->getTable()) . ' SET', // Removed space after SET
            $this->writeUpdateValues($update),
        ];

        AbstractBaseWriter::writeWhereCondition($update, $this->writer, $this->placeholderWriter, $parts);
        AbstractBaseWriter::writeLimitCondition($update, $this->placeholderWriter, $parts);
        $comment = AbstractBaseWriter::writeQueryComment($update);

        // Filter out empty strings that might result from conditions not being met
        $filteredParts = \array_filter($parts, fn (string $part) => $part !== '');
        return $comment . implode(' ', $filteredParts);
    }

    protected function writeUpdateValues(Update $update): string
    {
        $assigns = [];
        /** @var string $columnName */
        /** @var mixed $value */
        foreach ($update->getValues() as $columnName => $value) {
            // Ensure $columnName is a string for safety, though keys of an array are usually int or string.
            $newColumnArray = [(string)$columnName];
            $columnToWrite = $this->columnWriter->writeColumn(
                SyntaxFactory::createColumn($newColumnArray, $update->getTable())
            );

            $placeholderValue = $this->writer->writePlaceholderValue($value);

            $assigns[] = "{$columnToWrite} = {$placeholderValue}";
        }

        return \implode(', ', $assigns);
    }
}
