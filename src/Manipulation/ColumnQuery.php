<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/25/14
 * Time: 12:12 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class ColumnQuery.
 */
class ColumnQuery
{
    /** @var array<string|Column> */
    protected array $columns = [];

    /** @var array<array<string, Select>> */
    protected array $columnSelects = [];

    /** @var array<string, mixed> */
    protected array $columnValues = [];

    /** @var array<string, array{func: string, args: string[]}> */
    protected array $columnFuncs = [];

    protected bool $isCount = false;

    /**
     * @param array<string>|null $columns
     */
    public function __construct(
        protected Select $select,
        protected JoinQuery $joinQuery,
        ?array $columns = null
    ) {
        if (null === $columns) {
            $columns = [Column::ALL];
        }

        if (!empty($columns)) {
            $this->setColumns($columns);
        }
    }

    /**
     * Used by Select::__clone to update the internal reference to the new Select instance.
     */
    public function internalSetSelect(Select $select): void
    {
        $this->select = $select;
    }

    /**
     * Used by Select::__clone to update the internal reference to the new JoinQuery instance.
     */
    public function internalSetJoinQuery(JoinQuery $joinQuery): void
    {
        $this->joinQuery = $joinQuery;
    }

    public function limit(int $start, int $count = 0): Select
    {
        return $this->select->limit($start, $count);
    }

    public function where(string $whereOperator = 'AND'): Where
    {
        return $this->select->where($whereOperator);
    }

    /**
     * @param string|Table|null $table
     */
    public function orderBy(string $column, string $direction = OrderBy::ASC, mixed $table = null): Select
    {
        return $this->select->orderBy($column, $direction, $table);
    }

    /**
     * @param array<string> $columns
     */
    public function groupBy(array $columns): Select
    {
        return $this->select->groupBy($columns);
    }

    /**
     * Allows setting a Select query as a column value.
     * @param array<string, Select> $column e.g. ['alias' => SelectObject]
     */
    public function setSelectAsColumn(array $column): self
    {
        $this->columnSelects[] = $column;
        return $this;
    }

    /**
     * @return array<array<string, Select>>
     */
    public function getColumnSelects(): array
    {
        return $this->columnSelects;
    }

    /**
     * Allows setting a value to the select statement.
     */
    public function setValueAsColumn(mixed $value, string $alias): self
    {
        $this->columnValues[$alias] = $value;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getColumnValues(): array
    {
        return $this->columnValues;
    }

    /**
     * Allows calculation on columns using predefined SQL functions.
     * @param array<string> $arguments
     */
    public function setFunctionAsColumn(string $funcName, array $arguments, string $alias): self
    {
        $this->columnFuncs[$alias] = ['func' => $funcName, 'args' => $arguments];
        return $this;
    }

    /**
     * @return array<string, array{func: string, args: string[]}>
     */
    public function getColumnFuncs(): array
    {
        return $this->columnFuncs;
    }

    public function count(string $columnName = '*', string $alias = ''): self
    {
        $table = $this->select->getTable();
        $tableName = $table?->getName(); // Use nullsafe operator

        $count = 'COUNT(';
        $count .= ($columnName !== '*') ? ($tableName ? "{$tableName}.{$columnName}" : $columnName) : '*';
        $count .= ')';

        if ($alias !== '') {
            $count .= " AS " . $this->select->getBuilder()->writeColumnAlias($alias);
        }

        $this->columns = [$count]; // This will be a string, not a Column object yet
        $this->isCount = true;
        return $this;
    }

    public function isCount(): bool
    {
        return $this->isCount;
    }

    /**
     * @return array<Column>
     * @throws QueryException
     */
    public function getAllColumns(): array
    {
        $columns = $this->getColumns(); // This returns array<Column>

        foreach ($this->joinQuery->getJoins() as $join) {
            $joinCols = $join->getAllColumns(); // Assuming this also returns array<Column>
            $columns = \array_merge($columns, $joinCols);
        }
        return $columns;
    }

    /**
     * @return array<Column>
     * @throws QueryException
     */
    public function getColumns(): array
    {
        if (null === $this->select->getTable()) {
            throw new QueryException('No table specified for the Select instance');
        }
        // $this->columns can contain strings or Column::ALL
        // SyntaxFactory::createColumns expects array of strings or Column objects
        return SyntaxFactory::createColumns($this->columns, $this->select->getTable());
    }

    /**
     * Sets the column names used to write the SELECT statement.
     * If key is set, key is the column's alias. Value is always the column names.
     * @param array<string|Column> $columns
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }
}
