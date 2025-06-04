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

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class Select.
 */
class Select extends AbstractBaseQuery
{
    // Properties `$table` and `$where` are inherited from AbstractBaseQuery.
    // No need to redeclare them here if visibility and type are compatible.

    /** @var array<string> */
    protected array $groupBy = [];

    protected string $camelCaseTableName = ''; // Appears unused. Will keep for now.
    protected ?Where $having = null;
    protected string $havingOperator = 'AND';
    protected bool $isDistinct = false;

    protected JoinQuery $joinQuery;
    protected ColumnQuery $columnQuery;
    protected ?Select $parentQuery = null; // Changed from ParentQuery to ?Select

    /**
     * @param array<string>|null $columns
     */
    public function __construct(?string $table = null, ?array $columns = null)
    {
        if (null !== $table) {
            $this->setTable($table); // Uses parent's setTable
        }

        $this->joinQuery = new JoinQuery($this);
        $this->columnQuery = new ColumnQuery($this, $this->joinQuery, $columns);
    }

    /**
     * This __clone method will create an exact clone but without the object references due to the fact these
     * are lost in the process of serialization and un-serialization.
     */
    public function __clone(): void
    {
        // $this is already a shallow copy when __clone() is invoked.
        // We need to deep clone properties that are objects and ensure
        // their internal back-references point to this new cloned Select instance ($this).

        // Properties from AbstractBaseQuery
        if ($this->where !== null) {
            $this->where = clone $this->where; // Clones the Where object
            $this->where->setQuery($this);      // Re-parent: Where object now refers to the new Select
        }

        $newOrderBy = [];
        foreach ($this->orderBy as $orderByItem) {
            $newOrderBy[] = clone $orderByItem; // OrderBy objects are value objects, simple clone is fine
        }
        $this->orderBy = $newOrderBy;

        // Properties from Select
        if ($this->having !== null) {
            $this->having = clone $this->having; // Clones the Where object for HAVING
            $this->having->setQuery($this);       // Re-parent: Having's Where object refers to the new Select
        }

        // Deep clone composed query objects and update their back-references.
        // It's important to clone them before assigning to $this->joinQuery / $this->columnQuery
        // if other parts of the cloning process might depend on the old instances.
        // However, here we are directly replacing them.

        // Clone JoinQuery and re-parent it
        $clonedJoinQuery = clone $this->joinQuery;
        $clonedJoinQuery->internalSetSelect($this);
        $this->joinQuery = $clonedJoinQuery;

        // Clone ColumnQuery and re-parent it (and its JoinQuery reference)
        $clonedColumnQuery = clone $this->columnQuery;
        $clonedColumnQuery->internalSetSelect($this);
        $clonedColumnQuery->internalSetJoinQuery($this->joinQuery); // Point to the newly cloned JoinQuery
        $this->columnQuery = $clonedColumnQuery;

        // $parentQuery: Typically, parent references are not deep-cloned to avoid cycles
        // and because the clone is a new "child" in a sense, not a clone of the parent relationship.
        // So, $this->parentQuery (if not null) will still point to the original parent. This is usually desired.
    }

    public function partName(): string
    {
        return 'SELECT';
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function leftJoin(string $table, mixed $selfColumn = null, mixed $refColumn = null, array $columns = []): Select
    {
        return $this->joinQuery->leftJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function join(
        string $table,
        mixed $selfColumn = null,
        mixed $refColumn = null,
        array $columns = [],
        ?string $joinType = null
    ): Select {
        return $this->joinQuery->join($table, $selfColumn, $refColumn, $columns, $joinType);
    }

    public function joinCondition(): Where
    {
        return $this->joinQuery->joinCondition();
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     */
    public function addJoin(Select $select, mixed $selfColumn, mixed $refColumn): Select
    {
        return $this->joinQuery->addJoin($select, $selfColumn, $refColumn);
    }

    public function isJoin(bool $isJoin = true): self // Return type changed to self for consistency
    {
        $this->joinQuery->setJoin($isJoin);
        return $this;
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function rightJoin(string $table, mixed $selfColumn = null, mixed $refColumn = null, array $columns = []): Select
    {
        return $this->joinQuery->rightJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function crossJoin(string $table, mixed $selfColumn = null, mixed $refColumn = null, array $columns = []): Select
    {
        return $this->joinQuery->crossJoin($table, $selfColumn, $refColumn, $columns);
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function innerJoin(string $table, mixed $selfColumn = null, mixed $refColumn = null, array $columns = []): Select
    {
        return $this->joinQuery->innerJoin($table, $selfColumn, $refColumn, $columns);
    }

    public function on(): Where // Alias to joinCondition
    {
        return $this->joinQuery->joinCondition();
    }

    public function isJoinSelect(): bool
    {
        return $this->joinQuery->isJoin();
    }

    /**
     * @return array<Column>
     * @throws QueryException
     */
    public function getAllColumns(): array
    {
        return $this->columnQuery->getAllColumns();
    }

    /**
     * @return array<Column>
     * @throws QueryException
     */
    public function getColumns(): array
    {
        return $this->columnQuery->getColumns();
    }

    /**
     * @param array<string|Column> $columns
     */
    public function setColumns(array $columns): self // Return type self from ColumnQuery
    {
        $this->columnQuery->setColumns($columns);
        return $this;
    }

    /**
     * @param array<string, Select> $column
     */
    public function setSelectAsColumn(array $column): self // Return type self from ColumnQuery
    {
        $this->columnQuery->setSelectAsColumn($column);
        return $this;
    }

    /**
     * @return array<array<string, Select>>
     */
    public function getColumnSelects(): array
    {
        return $this->columnQuery->getColumnSelects();
    }

    public function setValueAsColumn(mixed $value, string $alias): self // Return type self from ColumnQuery
    {
        $this->columnQuery->setValueAsColumn($value, $alias);
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getColumnValues(): array
    {
        return $this->columnQuery->getColumnValues();
    }

    /**
     * @param array<string> $arguments
     */
    public function setFunctionAsColumn(string $funcName, array $arguments, string $alias): self // Return type self from ColumnQuery
    {
        $this->columnQuery->setFunctionAsColumn($funcName, $arguments, $alias);
        return $this;
    }

    /**
     * @return array<string, array{func: string, args: string[]}>
     */
    public function getColumnFuncs(): array
    {
        return $this->columnQuery->getColumnFuncs();
    }

    /**
     * Returns all the Where conditions to the BuilderInterface class in order to write the SQL WHERE statement.
     * @return array<Where>
     */
    public function getAllWheres(): array
    {
        /** @var array<Where> $wheres */
        $wheres = $this->getAllOperation($this->where, 'getAllWheres');
        return $wheres;
    }

    /**
     * @param Where|null $data Initial data (e.g., $this->where or $this->having)
     * @param string $operation Method name to call on joined Select objects (e.g., 'getAllWheres', 'getAllHavings')
     * @return array<mixed> Actually returns array of Where or Having objects based on usage
     */
    protected function getAllOperation(?object $data, string $operation): array
    {
        $collection = [];
        if (null !== $data) {
            $collection[] = $data;
        }

        /** @var Select $join */
        foreach ($this->joinQuery->getJoins() as $join) {
            // Ensure the method exists on the joined Select object before calling
            if (method_exists($join, $operation)) {
                $collection = \array_merge($collection, $join->$operation());
            }
        }
        return $collection;
    }

    /**
     * @return array<Where>
     */
    public function getAllHavings(): array
    {
        /** @var array<Where> $havings */ // Assuming Having conditions are structurally similar to Where for this method
        $havings = $this->getAllOperation($this->having, 'getAllHavings');
        return $havings;
    }

    public function count(string $columnName = '*', string $alias = ''): self // Return type self from ColumnQuery
    {
        $this->columnQuery->count($columnName, $alias);
        return $this;
    }

    public function isCount(): bool
    {
        return $this->columnQuery->isCount();
    }

    public function limit(int $start, int $count = 0): self
    {
        $this->limitStart = $start;
        $this->limitCount = $count;
        return $this;
    }

    /**
     * @return array<string, Select>
     */
    public function getAllJoins(): array
    {
        return $this->joinQuery->getAllJoins();
    }

    /**
     * @return array<Column>
     */
    public function getGroupBy(): array
    {
        return SyntaxFactory::createColumns($this->groupBy, $this->getTable());
    }

    /**
     * @param array<string> $columns
     */
    public function groupBy(array $columns): self
    {
        $this->groupBy = $columns;
        return $this;
    }

    public function getJoinCondition(): ?Where
    {
        return $this->joinQuery->getJoinCondition();
    }

    public function getJoinType(): ?string
    {
        return $this->joinQuery->getJoinType();
    }

    public function setJoinType(?string $joinType): self
    {
        $this->joinQuery->setJoinType($joinType);
        return $this;
    }

    /**
     * @throws QueryException
     */
    public function having(string $havingOperator = 'AND'): Where
    {
        if (!isset($this->having)) {
            $this->having = QueryFactory::createWhere($this);
        }

        if (!\in_array($havingOperator, [Where::CONJUNCTION_AND, Where::CONJUNCTION_OR], true)) {
            throw new QueryException(
                "Invalid conjunction specified, must be one of AND or OR, but '" . $havingOperator . "' was found."
            );
        }
        $this->havingOperator = $havingOperator;
        return $this->having;
    }

    public function getHavingOperator(): string
    {
        return $this->havingOperator;
    }

    public function distinct(): self
    {
        $this->isDistinct = true;
        return $this;
    }

    public function isDistinct(): bool
    {
        return $this->isDistinct;
    }

    /**
     * @return array<OrderBy>
     */
    public function getAllOrderBy(): array
    {
        return $this->orderBy; // Property $orderBy is already array<OrderBy> from AbstractBaseQuery
    }

    public function getParentQuery(): ?Select
    {
        return $this->parentQuery;
    }

    public function setParentQuery(Select $parentQuery): self
    {
        $this->parentQuery = $parentQuery;
        return $this;
    }

    /**
     * @param string|Table|null $table
     */
    public function orderBy(string $column, string $direction = OrderBy::ASC, mixed $table = null): self
    {
        parent::orderBy($column, $direction, $table); // Call parent to add to $this->orderBy
        if ($this->parentQuery !== null) { // Use null check
            $this->parentQuery->orderBy($column, $direction, $table ?? $this->getTable());
        }
        return $this;
    }
}
