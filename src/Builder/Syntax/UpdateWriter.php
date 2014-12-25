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

use NilPortugues\SqlQueryBuilder\Manipulation\QueryException;
use NilPortugues\SqlQueryBuilder\Manipulation\Update;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;

/**
 * Class UpdateWriter
 * @package NilPortugues\SqlQueryBuilder\BuilderInterface\Syntax
 */
class UpdateWriter extends AbstractBaseWriter
{
    /**
     * @param Update $update
     *
     * @throws QueryException
     * @return string
     */
    public function write(Update $update)
    {
        $values = $update->getValues();
        if (empty($values)) {
            throw new QueryException('No values to update in Update query.');
        }

        $comment = '';
        if ('' !== $update->getComment()) {
            $comment = $update->getComment();
        }

        $parts = array(
            "UPDATE ".$this->writer->writeTable($update->getTable())." SET ",
            $this->writeUpdateValues($update),
        );

        AbstractBaseWriter::writeWhereCondition($update, $this->writer, $this->placeholderWriter, $parts);
        AbstractBaseWriter::writeLimitCondition($update, $this->placeholderWriter, $parts);

        return $comment.implode(" ", $parts);
    }

    /**
     * @param Update $update
     *
     * @return string
     */
    private function writeUpdateValues(Update $update)
    {
        $assigns = [];
        foreach ($update->getValues() as $column => $value) {
            $newColumn = array($column);
            $column    = $this->columnWriter->writeColumn(SyntaxFactory::createColumn($newColumn, $update->getTable()));

            $value = $this->writer->writePlaceholderValue($value);

            $assigns[] = "$column = $value";
        }

        return implode(", ", $assigns);
    }
}
