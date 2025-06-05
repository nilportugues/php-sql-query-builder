<?php

declare(strict_types=1);

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class WhereWriter.
 */
class WhereWriter extends AbstractBaseWriter
{
    /** @var array<string, string> */
    protected array $matchMode = [
        'natural' => '(MATCH({{columnNames}}) AGAINST({{columnValues}}))',
        'boolean' => '(MATCH({{columnNames}}) AGAINST({{columnValues}} IN BOOLEAN MODE))',
        'query_expansion' => '(MATCH({{columnNames}}) AGAINST({{columnValues}} WITH QUERY EXPANSION))',
    ];

    public function writeWhere(Where $where): string
    {
        $clauses = $this->writeWhereClauses($where);
        $clauses = \array_filter($clauses); // Remove empty strings

        if (empty($clauses)) {
            return '';
        }

        return \implode($this->writer->writeConjunction($where->getConjunction()), $clauses);
    }

    /**
     * @return array<string>
     */
    public function writeWhereClauses(Where $where): array
    {
        $whereArray = [];

        $this->writeWhereMatches($where, $whereArray); // Appends to $whereArray
        $whereArray = \array_merge($whereArray, $this->writeWhereIns($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereNotIns($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereBetweens($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereNotBetweens($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereComparisons($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereIsNulls($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereIsNotNulls($where));
        $whereArray = \array_merge($whereArray, $this->writeWhereBooleans($where));
        $whereArray = \array_merge($whereArray, $this->writeExists($where));
        $whereArray = \array_merge($whereArray, $this->writeNotExists($where));
        $whereArray = \array_merge($whereArray, $this->writeSubWheres($where));

        return $whereArray;
    }

    /**
     * @param array<string> $whereArray
     */
    protected function writeWhereMatches(Where $where, array &$whereArray): void
    {
        $matches = [];
        /** @var array{columns: array<string>, values: array<string|int|float>, mode: string} $matchData */
        foreach ($where->getMatches() as $matchData) {
            $columns = SyntaxFactory::createColumns($matchData['columns'], $where->getTable());
            $columnNamesString = $this->getColumnNames($columns);

            $columnValues = [(string)\implode(' ', $matchData['values'])];
            $columnValuesString = \implode(', ', $this->writer->writeValues($columnValues));

            $matches[] = \str_replace(
                ['{{columnNames}}', '{{columnValues}}'],
                [$columnNamesString, $columnValuesString],
                $this->matchMode[$matchData['mode']]
            );
        }
        $whereArray = \array_merge($whereArray, $matches);
    }

    /**
     * @param array<Column> $columns
     */
    protected function getColumnNames(array $columns): string
    {
        $columnNames = [];
        foreach ($columns as $column) {
            $columnNames[] = $this->columnWriter->writeColumn($column);
        }
        return \implode(', ', $columnNames);
    }

    /**
     * @return array<string>
     */
    protected function writeWhereIns(Where $where): array
    {
        return $this->writeWhereIn($where, 'getIns', 'IN');
    }

    /**
     * @return array<string>
     */
    protected function writeWhereIn(Where $where, string $method, string $operation): array
    {
        $collection = [];
        /** @var array<string, array<mixed>> $ins */
        $ins = $where->$method();

        /** @var string $columnName */
        /** @var array<mixed> $values */
        foreach ($ins as $columnName => $values) {
            $newColumnArray = [$columnName];
            $columnToWrite = SyntaxFactory::createColumn($newColumnArray, $where->getTable());
            $columnString = $this->columnWriter->writeColumn($columnToWrite);

            $writtenValues = $this->writer->writeValues($values);
            $valuesString = \implode(', ', $writtenValues);

            $collection[] = "({$columnString} {$operation} ({$valuesString}))";
        }
        return $collection;
    }

    /**
     * @return array<string>
     */
    protected function writeWhereNotIns(Where $where): array
    {
        return $this->writeWhereIn($where, 'getNotIns', 'NOT IN');
    }

    /**
     * @return array<string>
     */
    protected function writeWhereBetweens(Where $where): array
    {
        $output = [];
        /** @var array{subject: Column, a: mixed, b: mixed} $betweenData */
        foreach ($where->getBetweens() as $betweenData) {
            $output[] = '('
                . $this->columnWriter->writeColumn($betweenData['subject'])
                . ' BETWEEN '
                . $this->writer->writePlaceholderValue($betweenData['a'])
                . ' AND '
                . $this->writer->writePlaceholderValue($betweenData['b'])
                . ')';
        }
        return $output;
    }

    /**
     * @return array<string>
     */
    protected function writeWhereNotBetweens(Where $where): array
    {
        $output = [];
        /** @var array{subject: Column, a: mixed, b: mixed} $notBetweenData */
        foreach ($where->getNotBetweens() as $notBetweenData) {
            $output[] = '('
                . $this->columnWriter->writeColumn($notBetweenData['subject'])
                . ' NOT BETWEEN '
                . $this->writer->writePlaceholderValue($notBetweenData['a'])
                . ' AND '
                . $this->writer->writePlaceholderValue($notBetweenData['b'])
                . ')';
        }
        return $output;
    }

    /**
     * @return array<string>
     */
    protected function writeWhereComparisons(Where $where): array
    {
        $output = [];
        /** @var array{subject: Column|Select|mixed, conjunction: string, target: Column|Select|mixed}|string $comparisonData */
        foreach ($where->getComparisons() as $comparisonData) {
            if (!\is_array($comparisonData)) { // This handles literal strings
                $output[] = (string)$comparisonData;
                continue;
            }

            $subjectStr = $this->writeWherePartialCondition($comparisonData['subject']);
            $conjunctionStr = $this->writer->writeConjunction((string)$comparisonData['conjunction']); // Simplified: removed ?? ''
            $targetStr = $this->writeWherePartialCondition($comparisonData['target']);

            $output[] = "({$subjectStr}{$conjunctionStr}{$targetStr})";
        }
        return $output;
    }

    protected function writeWherePartialCondition(mixed $subject): string
    {
        if ($subject instanceof Column) {
            return $this->columnWriter->writeColumn($subject);
        }
        if ($subject instanceof Select) {
            $selectWriter = WriterFactory::createSelectWriter($this->writer, $this->placeholderWriter);
            return '(' . $selectWriter->write($subject) . ')';
        }
        return $this->writer->writePlaceholderValue($subject);
    }

    /**
     * @return array<string>
     */
    protected function writeWhereIsNulls(Where $where): array
    {
        return $this->writeWhereIsNullable($where, 'getNull', 'writeIsNull');
    }

    /**
     * @return array<string>
     */
    protected function writeWhereIsNullable(Where $where, string $getMethod, string $writeMethod): array
    {
        $output = [];
        /** @var array<array{subject: Column}> $items */
        $items = $where->$getMethod();
        foreach ($items as $item) {
            $output[] = '('
                . $this->columnWriter->writeColumn($item['subject'])
                . $this->writer->$writeMethod() . ')';
        }
        return $output;
    }

    /**
     * @return array<string>
     */
    protected function writeWhereIsNotNulls(Where $where): array
    {
        return $this->writeWhereIsNullable($where, 'getNotNull', 'writeIsNotNull');
    }

    /**
     * @return array<string>
     */
    protected function writeWhereBooleans(Where $where): array
    {
        $output = [];
        /** @var array{subject: Column, value: mixed} $booleanData */
        foreach ($where->getBooleans() as $booleanData) {
            $columnStr = $this->columnWriter->writeColumn($booleanData['subject']);
            $valuePlaceholder = $this->placeholderWriter->add($booleanData['value']);
            $output[] = '(ISNULL(' . $columnStr . ', 0) = ' . $valuePlaceholder . ')';
        }
        return $output;
    }

    /**
     * @return array<string>
     */
    protected function writeExists(Where $where): array
    {
        return $this->writeExistence($where, 'getExists', 'EXISTS');
    }

    /**
     * @return array<string>
     */
    protected function writeExistence(Where $where, string $method, string $operation): array
    {
        $output = [];
        /** @var Select $select */
        foreach ($where->$method() as $select) {
            $output[] = "{$operation} (" . $this->writer->write($select, false) . ')';
        }
        return $output;
    }

    /**
     * @return array<string>
     */
    protected function writeNotExists(Where $where): array
    {
        return $this->writeExistence($where, 'getNotExists', 'NOT EXISTS');
    }

    /**
     * @return array<string>
     */
    protected function writeSubWheres(Where $where): array
    {
        $output = [];
        /** @var Where $subWhere */
        foreach ($where->getSubWheres() as $subWhere) {
            $output[] = "({$this->writeWhere($subWhere)})";
        }
        return $output;
    }
}
