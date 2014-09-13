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
use NilPortugues\SqlQueryBuilder\Manipulation\QueryException;
use NilPortugues\SqlQueryBuilder\Manipulation\Update;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;

/**
 * Class UpdateWriter
 * @package NilPortugues\SqlQueryBuilder\BuilderInterface\Syntax
 */
class UpdateWriter
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @var PlaceholderWriter
     */
    private $placeholderWriter;

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
        $this->writer            = $writer;
        $this->placeholderWriter = $placeholder;

        $this->columnWriter = WriterFactory::createColumnWriter($writer, $placeholder);
    }

    /**
     * @param Update $update
     *
     * @throws QueryException
     * @return string
     */
    public function writeUpdate(Update $update)
    {
        $values = $update->getValues();
        if (empty($values)) {
            throw new QueryException('No values to update in Update query.');
        }

        $parts = array
        (
            "UPDATE ".$this->writer->writeTable($update->getTable())." SET ",
            $this->writeUpdateValues($update),
        );

        if (!is_null($update->getWhere())) {

            $whereWriter = WriterFactory::createWhereWriter($this->writer, $this->placeholderWriter);
            $parts[]     = " WHERE {$whereWriter->writeWhere($update->getWhere())}";
        }

        if (!is_null($update->getLimitStart())) {
            $start   = $this->placeholderWriter->add($update->getLimitStart());
            $parts[] = "LIMIT {$start}";
        }

        return implode(" ", $parts);
    }

    /**
     * @param Update $update
     *
     * @return string
     */
    private function writeUpdateValues(Update $update)
    {
        $assigns = array();
        foreach ($update->getValues() as $column => $value) {

            $newColumn = array($column);
            $column    = $this->columnWriter->writeColumn(SyntaxFactory::createColumn($newColumn, $update->getTable()));

            $value = $this->writer->writePlaceholderValue($value);

            $assigns[] = "$column = $value";
        }

        return implode(", ", $assigns);
    }
}
