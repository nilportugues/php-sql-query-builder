<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/25/14
 * Time: 11:41 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class JoinQuery.
 */
class JoinQuery
{
    final public const JOIN_LEFT = 'LEFT';
    final public const JOIN_RIGHT = 'RIGHT';
    final public const JOIN_INNER = 'INNER';
    final public const JOIN_CROSS = 'CROSS';

    protected ?Where $joinCondition = null;
    protected bool $isJoin = false;
    protected ?string $joinType = null;

    /** @var array<string, Select> */
    protected array $joins = [];

    public function __construct(protected Select $select)
    {
    }

    /**
     * Used by Select::__clone to update the internal reference to the new Select instance.
     */
    public function internalSetSelect(Select $select): void
    {
        $this->select = $select;
    }

    public function setTable(string $table): self
    {
        $this->select->setTable($table);
        return $this;
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function leftJoin(
        string $table,
        mixed $selfColumn = null,
        mixed $refColumn = null,
        array $columns = []
    ): Select {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_LEFT);
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
        if (!isset($this->joins[$table])) {
            $select = QueryFactory::createSelect($table);
            $select->setColumns($columns);
            if ($joinType !== null) {
                $select->setJoinType($joinType);
            }
            $select->setParentQuery($this->select);
            $this->addJoin($select, $selfColumn, $refColumn);
        }
        return $this->joins[$table];
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     */
    public function addJoin(Select $select, mixed $selfColumn, mixed $refColumn): Select
    {
        $select->isJoin(true);
        $table = $select->getTable()?->getName(); // Nullsafe access to table name

        if ($table === null) {
            // Or throw an exception, a join must be on a table.
            // This case should ideally not happen if Select object is always valid.
            return $select; // Early return or throw
        }

        if (!isset($this->joins[$table])) {
            $selfColInstance = $selfColumn;
            if (!$selfColumn instanceof Column) {
                $selfColInstance = SyntaxFactory::createColumn([(string)$selfColumn], $this->select->getTable());
            }

            // Ensure $refColumn is also a Column instance or a string that can be resolved by `equals`
            // The `equals` method in Where should handle mixed types or specific types for columns.
            $select->joinCondition()->equals($refColumn, $selfColInstance);
            $this->joins[$table] = $select;
        }
        return $this->joins[$table];
    }

    /**
     * Transforms Select in a joint.
     */
    public function setJoin(bool $isJoin = true): self
    {
        $this->isJoin = $isJoin;
        return $this;
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function rightJoin(
        string $table,
        mixed $selfColumn = null,
        mixed $refColumn = null,
        array $columns = []
    ): Select {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_RIGHT);
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function crossJoin(
        string $table,
        mixed $selfColumn = null,
        mixed $refColumn = null,
        array $columns = []
    ): Select {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_CROSS);
    }

    /**
     * @param string|Column|null $selfColumn
     * @param string|Column|null $refColumn
     * @param array<string> $columns
     */
    public function innerJoin(
        string $table,
        mixed $selfColumn = null,
        mixed $refColumn = null,
        array $columns = []
    ): Select {
        return $this->join($table, $selfColumn, $refColumn, $columns, self::JOIN_INNER);
    }

    /**
     * Alias to joinCondition.
     */
    public function on(): Where
    {
        return $this->joinCondition();
    }

    /**
     * WHERE constrains used for the ON clause of a (LEFT/RIGHT/INNER/CROSS) JOIN.
     */
    public function joinCondition(): Where
    {
        if (!isset($this->joinCondition)) {
            $this->joinCondition = QueryFactory::createWhere($this->select);
        }
        return $this->joinCondition;
    }

    public function isJoinSelect(): bool
    {
        return $this->isJoin;
    }

    public function isJoin(): bool // Alias
    {
        return $this->isJoin;
    }

    public function getJoinCondition(): ?Where
    {
        return $this->joinCondition;
    }

    public function setJoinCondition(Where $joinCondition): self
    {
        $this->joinCondition = $joinCondition;
        return $this;
    }

    public function getJoinType(): ?string
    {
        return $this->joinType;
    }

    public function setJoinType(?string $joinType): self
    {
        $this->joinType = $joinType;
        return $this;
    }

    /**
     * @return array<string, Select>
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @param array<string, Select> $joins
     */
    public function setJoins(array $joins): self
    {
        $this->joins = $joins;
        return $this;
    }

    /**
     * @return array<string, Select>
     */
    public function getAllJoins(): array
    {
        $allJoins = $this->joins; // Start with current level joins

        /** @var Select $joinSelect */
        foreach ($this->joins as $joinSelect) {
            // Recursively get joins from joined Select objects
            if ($joinSelect instanceof Select) { // Check if it's a Select object
                $allJoins = \array_merge($allJoins, $joinSelect->getAllJoins());
            }
        }
        return $allJoins;
    }
}
