<?php

namespace NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Manipulation\Select;
use NilPortugues\SqlQueryBuilder\Syntax\Column;
use NilPortugues\SqlQueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\SqlQueryBuilder\Syntax\Where;

/**
 * Class WhereWriter
 * @package NilPortugues\SqlQueryBuilder\BuilderInterface\Syntax
 */
class WhereWriter extends AbstractBaseWriter
{
    /**
     * @var array
     */
    protected $matchMode = [
        'natural' => "(MATCH({{columnNames}}) AGAINST({{columnValues}}))",
        'boolean' => "(MATCH({{columnNames}}) AGAINST({{columnValues}} IN BOOLEAN MODE))",
        'query_expansion' => "(MATCH({{columnNames}}) AGAINST({{columnValues}} WITH QUERY EXPANSION))"
    ];

    /**
     * @param Where $where
     *
     * @return string
     */
    public function writeWhere(Where $where)
    {
        $clauses = $this->writeWhereClauses($where);
        $clauses = array_filter($clauses);

        if (empty($clauses)) {
            return '';
        }

        return implode($this->writer->writeConjunction($where->getConjunction()), $clauses);
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    public function writeWhereClauses(Where $where)
    {
        $matches     = $this->writeWhereMatches($where);
        $ins         = $this->writeWhereIns($where);
        $notIns      = $this->writeWhereNotIns($where);
        $betweens    = $this->writeWhereBetweens($where);
        $comparisons = $this->writeWhereComparisons($where);
        $isNulls     = $this->writeWhereIsNulls($where);
        $isNotNulls  = $this->writeWhereIsNotNulls($where);
        $booleans    = $this->writeWhereBooleans($where);
        $exists      = $this->writeExists($where);
        $notExists      = $this->writeNotExists($where);
        $subWheres   = $where->getSubWheres();

        array_walk(
            $subWheres,
            function (&$subWhere) {
                $subWhere = "({$this->writeWhere($subWhere)})";
            }
        );

        $clauses = array_merge(
            $matches,
            $ins,
            $notIns,
            $betweens,
            $comparisons,
            $isNulls,
            $isNotNulls,
            $booleans,
            $exists,
            $notExists,
            $subWheres
        );

        return $clauses;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereMatches(Where $where)
    {
        $matches = [];

        foreach ($where->getMatches() as $values) {
            $columns = SyntaxFactory::createColumns($values['columns'], $where->getTable());

            $columnNames = [];
            foreach ($columns as &$column) {
                $columnNames[] = $this->columnWriter->writeColumn($column);
            }
            $columnNames = implode(', ', $columnNames);

            $columnValues = array(implode(" ", $values['values']));
            $columnValues = implode(", ", $this->writer->writeValues($columnValues));

            $matches[] = str_replace(
                ['{{columnNames}}', '{{columnValues}}'],
                [$columnNames, $columnValues],
                $this->matchMode[$values['mode']]
            );

        }

        return $matches;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereIns(Where $where)
    {
        return $this->writeWhereIn($where, 'getIns', 'IN');
    }

    /**
     * @param Where  $where
     * @param string $method
     * @param string $operation
     *
     * @return array
     */
    protected function writeWhereIn(Where $where, $method, $operation)
    {
        $collection = [];

        foreach ($where->$method() as $column => $values) {
            $newColumn = array($column);
            $column    = SyntaxFactory::createColumn($newColumn, $where->getTable());
            $column    = $this->columnWriter->writeColumn($column);

            $values = $this->writer->writeValues($values);
            $values = implode(", ", $values);

            $collection[] = "({$column} $operation ({$values}))";
        }

        return $collection;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereNotIns(Where $where)
    {
        return $this->writeWhereIn($where, 'getNotIns', 'NOT IN');
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereBetweens(Where $where)
    {
        $between = $where->getBetweens();
        array_walk(
            $between,
            function (&$between) {

                $between = "("
                    .$this->columnWriter->writeColumn($between["subject"])
                    ." BETWEEN "
                    .$this->writer->writePlaceholderValue($between["a"])
                    ." AND "
                    .$this->writer->writePlaceholderValue($between["b"])
                    .")";
            }
        );

        return $between;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereComparisons(Where $where)
    {
        $comparisons = $where->getComparisons();
        array_walk(
            $comparisons,
            function (&$comparison) {

                $str = $this->writeWherePartialCondition($comparison["subject"]);
                $str .= $this->writer->writeConjunction($comparison["conjunction"]);
                $str .= $this->writeWherePartialCondition($comparison["target"]);

                $comparison = "($str)";
            }
        );

        return $comparisons;
    }

    /**
     * @param $subject
     *
     * @return string
     */
    protected function writeWherePartialCondition(&$subject)
    {
        if ($subject instanceof Column) {
            $str = $this->columnWriter->writeColumn($subject);
        } elseif ($subject instanceof Select) {
            $selectWriter = WriterFactory::createSelectWriter($this->writer, $this->placeholderWriter);
            $str          = '('.$selectWriter->write($subject).')';
        } else {
            $str = $this->writer->writePlaceholderValue($subject);
        }

        return $str;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereIsNulls(Where $where)
    {
        return $this->writeWhereIsNullable($where, 'getNull', 'writeIsNull');
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereIsNotNulls(Where $where)
    {
        return $this->writeWhereIsNullable($where, 'getNotNull', 'writeIsNotNull');
    }

    /**
     * @param Where  $where
     * @param string $getMethod
     * @param string $writeMethod
     *
     * @return array
     */
    protected function writeWhereIsNullable(Where $where, $getMethod, $writeMethod)
    {
        $collection = $where->$getMethod();

        array_walk(
            $collection,
            function (&$collection) use ($writeMethod) {
                $collection =
                    "(".$this->columnWriter->writeColumn($collection["subject"])
                    .$this->writer->$writeMethod().")";
            }
        );

        return $collection;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeWhereBooleans(Where $where)
    {
        $booleans          = $where->getBooleans();
        $placeholderWriter = $this->placeholderWriter;

        array_walk(
            $booleans,
            function (&$boolean) use (&$placeholderWriter) {
                $column = $this->columnWriter->writeColumn($boolean["subject"]);
                $value  = $this->placeholderWriter->add($boolean["value"]);

                $boolean = "(ISNULL(".$column.", 0) = ".$value.")";
            }
        );

        return $booleans;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeExists(Where $where)
    {
        return $this->writeExistence($where, 'getExists', 'EXISTS');
    }

    /**
     * @param Where  $where
     * @param string $method
     * @param string $operation
     *
     * @return array
     */
    protected function writeExistence(Where $where, $method, $operation)
    {
        $exists = [];

        foreach ($where->$method() as $select) {
            $exists[] = "$operation (".$this->writer->write($select, false).")";
        }

        return $exists;
    }

    /**
     * @param Where $where
     *
     * @return array
     */
    protected function writeNotExists(Where $where)
    {
        return $this->writeExistence($where, 'getNotExists', 'NOT EXISTS');
    }
}
