<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Syntax;

use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryFactory;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryInterface;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class Where.
 */
class Where
{
    final public const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';
    final public const OPERATOR_GREATER_THAN = '>';
    final public const OPERATOR_LESS_THAN_OR_EQUAL = '<=';
    final public const OPERATOR_LESS_THAN = '<';
    final public const OPERATOR_LIKE = 'LIKE';
    final public const OPERATOR_NOT_LIKE = 'NOT LIKE';
    final public const OPERATOR_EQUAL = '=';
    final public const OPERATOR_NOT_EQUAL = '<>';
    final public const CONJUNCTION_AND = 'AND';
    final public const CONJUNCTION_AND_NOT = 'AND NOT';
    final public const CONJUNCTION_OR = 'OR';
    final public const CONJUNCTION_OR_NOT = 'OR NOT';
    final public const CONJUNCTION_EXISTS = 'EXISTS';
    final public const CONJUNCTION_NOT_EXISTS = 'NOT EXISTS';

    /** @var array<array{subject: Column|Select|string, conjunction: string, target: Column|Select|mixed}|string> */
    protected array $comparisons = [];

    /** @var array<array{subject: Column, a: mixed, b: mixed}> */
    protected array $betweens = [];

    /** @var array<array{subject: Column}> */
    protected array $isNull = [];

    /** @var array<array{subject: Column}> */
    protected array $isNotNull = [];

    /** @var array<array{subject: Column, value: mixed}> */
    protected array $booleans = [];

    /** @var array<array{columns: array<string>, values: array<mixed>, mode: string}> */
    protected array $match = [];

    /** @var array<string, array<mixed>> */
    protected array $ins = [];

    /** @var array<string, array<mixed>> */
    protected array $notIns = [];

    /** @var array<Where> */
    protected array $subWheres = [];

    protected string $conjunction = self::CONJUNCTION_AND;
    protected ?Table $table = null; // Table context for columns created in this Where instance, if any.

    /** @var array<Select> */
    protected array $exists = [];

    /** @var array<Select> */
    protected array $notExists = [];

    /** @var array<array{subject: Column, a: mixed, b: mixed}> */
    protected array $notBetweens = [];

    public function __construct(protected QueryInterface $query)
    {
    }

    /**
     * Deep copy for nested references.
     */
    public function __clone(): void
    {
        // When a Where object is cloned (e.g. as part of Select cloning),
        // its internal object properties might need deep cloning.
        // The $query property (QueryInterface) is the most critical.
        // It should refer to the new master query if the master query itself was cloned.
        // This re-parenting is typically handled by the master query's __clone method
        // by calling a setter like $clonedWhere->setQuery($newMasterQuery).

        // Clone sub-wheres:
        foreach ($this->subWheres as $key => $subWhere) {
            $this->subWheres[$key] = clone $subWhere;
            // The cloned subWhere's $query reference also needs to point to this Where's $query (the master query).
            // This implies subWhere's $query should be this Where object's $query.
            // This is complex because $this->query points to the *main* query (e.g. Select)
            // not the parent Where for a subWhere. QueryFactory::createWhere($this->query) sets this.
            // So, after cloning a subWhere, its $query reference should remain pointing to the same main query.
            // If the main query is cloned, Select::__clone will call $this->where->setQuery($newMainQuery).
        }
    }

    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
    }

    public function isEmpty(): bool
    {
        $empty = \array_merge(
            $this->comparisons,
            $this->booleans,
            $this->betweens,
            $this->isNotNull,
            $this->isNull,
            $this->ins,
            $this->notIns,
            $this->subWheres,
            $this->exists,
            $this->notExists, // Added notExists to the check
            $this->match // Added match to the check
        );
        return [] === $empty; // More direct check for empty array
    }

    public function getConjunction(): string
    {
        return $this->conjunction;
    }

    /**
     * @throws QueryException
     */
    public function conjunction(string $operator): self
    {
        if (!\in_array(
            $operator,
            [self::CONJUNCTION_AND, self::CONJUNCTION_OR, self::CONJUNCTION_OR_NOT, self::CONJUNCTION_AND_NOT],
            true // Strict comparison
        )) {
            throw new QueryException(
                "Invalid conjunction specified, must be one of AND, OR, OR NOT, AND NOT, but '{$operator}' was found."
            );
        }
        $this->conjunction = $operator;
        return $this;
    }

    /**
     * @return array<Where>
     */
    public function getSubWheres(): array
    {
        return $this->subWheres;
    }

    public function subWhere(string $operator = self::CONJUNCTION_OR): Where
    {
        /** @var Where $filter */
        $filter = QueryFactory::createWhere($this->query);
        $filter->conjunction($operator);
        $tableForSubWhere = $this->getTable();
        if ($tableForSubWhere !== null) {
            $filter->setTable($tableForSubWhere);
        }
        $this->subWheres[] = $filter;
        return $filter;
    }

    public function getTable(): ?Table
    {
        // If a table has been explicitly set on this Where instance (e.g., for a subWhere), use it.
        // Otherwise, defer to the main query's table.
        return $this->table ?? $this->query->getTable();
    }

    /**
     * Used for subWhere query building primarily.
     */
    public function setTable(Table $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * equals alias.
     * @param string|Column|Select $column
     */
    public function eq(string|Column|Select $column, mixed $value): self
    {
        return $this->equals($column, $value);
    }

    /**
     * @param string|Column|Select $column
     */
    public function equals(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_EQUAL);
    }

    /**
     * @param string|Column|Select $column
     */
    protected function compare(string|Column|Select $column, mixed $value, string $operator): self
    {
        $preparedColumn = $this->prepareColumn($column);
        $this->comparisons[] = [
            'subject' => $preparedColumn,
            'conjunction' => $operator,
            'target' => $value,
        ];
        return $this;
    }

    protected function prepareColumn(string|Column|Select $column): Column|Select
    {
        if ($column instanceof Select || $column instanceof Column) {
            return $column;
        }
        // If it's a string, create a Column object.
        return SyntaxFactory::createColumn([$column], $this->getTable());
    }

    /** @param string|Column|Select $column */
    public function notEquals(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_NOT_EQUAL);
    }

    /** @param string|Column|Select $column */
    public function greaterThan(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_GREATER_THAN);
    }

    /** @param string|Column|Select $column */
    public function greaterThanOrEqual(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_GREATER_THAN_OR_EQUAL);
    }

    /** @param string|Column|Select $column */
    public function lessThan(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_LESS_THAN);
    }

    /** @param string|Column|Select $column */
    public function lessThanOrEqual(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_LESS_THAN_OR_EQUAL);
    }

    /** @param string|Column|Select $column */
    public function like(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_LIKE);
    }

    /** @param string|Column|Select $column */
    public function notLike(string|Column|Select $column, mixed $value): self
    {
        return $this->compare($column, $value, self::OPERATOR_NOT_LIKE);
    }

    /**
     * @param array<string> $columns
     * @param array<mixed> $values
     */
    public function match(array $columns, array $values): self
    {
        return $this->genericMatch($columns, $values, 'natural');
    }

    /**
     * @param array<string> $columns
     * @param array<mixed> $values
     */
    protected function genericMatch(array $columns, array $values, string $mode): self
    {
        $this->match[] = [
            'columns' => $columns,
            'values' => $values,
            'mode' => $mode,
        ];
        return $this;
    }

    public function asLiteral(string $literal): self
    {
        $this->comparisons[] = $literal;
        return $this;
    }

    /**
     * @param array<string> $columns
     * @param array<mixed> $values
     */
    public function matchBoolean(array $columns, array $values): self
    {
        return $this->genericMatch($columns, $values, 'boolean');
    }

    /**
     * @param array<string> $columns
     * @param array<mixed> $values
     */
    public function matchWithQueryExpansion(array $columns, array $values): self
    {
        return $this->genericMatch($columns, $values, 'query_expansion');
    }

    /**
     * @param array<mixed> $values
     */
    public function in(string $columnName, array $values): self
    {
        $this->ins[$columnName] = $values;
        return $this;
    }

    /**
     * @param array<mixed> $values
     */
    public function notIn(string $columnName, array $values): self
    {
        $this->notIns[$columnName] = $values;
        return $this;
    }

    /** @param string|Column|Select $column */
    public function between(string|Column|Select $column, mixed $a, mixed $b): self
    {
        $preparedColumn = $this->prepareColumn($column);
        $this->betweens[] = ['subject' => $preparedColumn, 'a' => $a, 'b' => $b];
        return $this;
    }

    /** @param string|Column|Select $column */
    public function notBetween(string|Column|Select $column, mixed $a, mixed $b): self
    {
        $preparedColumn = $this->prepareColumn($column);
        $this->notBetweens[] = ['subject' => $preparedColumn, 'a' => $a, 'b' => $b];
        return $this;
    }

    /** @param string|Column|Select $column */
    public function isNull(string|Column|Select $column): self
    {
        $preparedColumn = $this->prepareColumn($column);
        $this->isNull[] = ['subject' => $preparedColumn];
        return $this;
    }

    /** @param string|Column|Select $column */
    public function isNotNull(string|Column|Select $column): self
    {
        $preparedColumn = $this->prepareColumn($column);
        $this->isNotNull[] = ['subject' => $preparedColumn];
        return $this;
    }

    /** @param string|Column|Select $column */
    public function addBitClause(string|Column|Select $column, mixed $value): self
    {
        $preparedColumn = $this->prepareColumn($column);
        $this->booleans[] = ['subject' => $preparedColumn, 'value' => $value];
        return $this;
    }

    public function exists(Select $select): self
    {
        $this->exists[] = $select;
        return $this;
    }

    /** @return array<Select> */
    public function getExists(): array
    {
        return $this->exists;
    }

    public function notExists(Select $select): self
    {
        $this->notExists[] = $select;
        return $this;
    }

    /** @return array<Select> */
    public function getNotExists(): array
    {
        return $this->notExists;
    }

    /** @return array<array{columns: array<string>, values: array<mixed>, mode: string}> */
    public function getMatches(): array
    {
        return $this->match;
    }

    /** @return array<string, array<mixed>> */
    public function getIns(): array
    {
        return $this->ins;
    }

    /** @return array<string, array<mixed>> */
    public function getNotIns(): array
    {
        return $this->notIns;
    }

    /** @return array<array{subject: Column, a: mixed, b: mixed}> */
    public function getBetweens(): array
    {
        return $this->betweens;
    }

    /** @return array<array{subject: Column, a: mixed, b: mixed}> */
    public function getNotBetweens(): array
    {
        return $this->notBetweens;
    }

    /** @return array<array{subject: Column, value: mixed}> */
    public function getBooleans(): array
    {
        return $this->booleans;
    }

    /** @return array<array{subject: Column|Select|string, conjunction: string, target: Column|Select|mixed}|string> */
    public function getComparisons(): array
    {
        return $this->comparisons;
    }

    /** @return array<array{subject: Column}> */
    public function getNotNull(): array
    {
        return $this->isNotNull;
    }

    /** @return array<array{subject: Column}> */
    public function getNull(): array
    {
        return $this->isNull;
    }

    public function end(): QueryInterface
    {
        return $this->query;
    }
}
