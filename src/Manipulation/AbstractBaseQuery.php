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

use NilPortugues\Sql\QueryBuilder\Builder\BuilderInterface;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\QueryPartInterface;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use NilPortugues\Sql\QueryBuilder\Manipulation\Join; // Assuming Join class exists for $joins array

/**
 * Class AbstractBaseQuery.
 */
abstract class AbstractBaseQuery implements QueryInterface, QueryPartInterface
{
    protected string $comment = '';
    protected ?BuilderInterface $builder = null;
    protected ?string $table = null;
    protected string $whereOperator = 'AND';
    protected ?Where $where = null;

    /** @var array<Join> */
    protected array $joins = []; // Assuming Join objects are stored here. If not, adjust type.

    protected ?int $limitStart = null;
    protected ?int $limitCount = null;

    /** @var array<OrderBy> */
    protected array $orderBy = [];

    protected function filter(): Where
    {
        if (!isset($this->where)) {
            $this->where = QueryFactory::createWhere($this);
        }
        return $this->where;
    }

    /**
     * Stores the builder that created this query.
     */
    final public function setBuilder(BuilderInterface $builder): static
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @throws \RuntimeException when builder has not been injected
     */
    final public function getBuilder(): BuilderInterface
    {
        if (null === $this->builder) {
            throw new \RuntimeException('Query builder has not been injected with setBuilder');
        }
        return $this->builder;
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     */
    public function __toString(): string
    {
        try {
            return $this->getSql();
        } catch (\Exception $e) {
            // It's generally better to let exceptions bubble up or log them,
            // but keeping original behavior of returning a string.
            return \sprintf('[%s] %s', \get_class($e), $e->getMessage());
        }
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     * Optionally can return the SQL with formatted structure.
     * @throws \ReflectionException
     */
    public function getSql(bool $formatted = false): string
    {
        if ($formatted) {
            return $this->getBuilder()->writeFormatted($this);
        }
        return $this->getBuilder()->write($this);
    }

    abstract public function partName(): string;

    public function getWhere(): ?Where
    {
        return $this->where;
    }

    public function setWhere(Where $where): self
    {
        $this->where = $where;
        return $this;
    }

    public function getTable(): ?Table
    {
        if (null === $this->table) {
            return null;
        }
        return SyntaxFactory::createTable([$this->table]);
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $whereOperator = 'AND'): Where
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }
        $this->where->conjunction($whereOperator);
        return $this->where;
    }

    public function getWhereOperator(): string
    {
        if (!isset($this->where)) {
            $this->where = $this->filter(); // Ensure where is initialized
        }
        // Since $this->where is guaranteed to be non-null here, -> can be used.
        // getConjunction() itself on Where object should handle if its internal conjunction is null/default.
        return $this->where->getConjunction();
    }

    /**
     * @param string|Table|null $table
     */
    public function orderBy(string $column, string $direction = OrderBy::ASC, mixed $table = null): self
    {
        $tableObject = null;
        if ($table instanceof Table) {
            $tableObject = $table;
        } elseif (\is_string($table)) {
            // If a string table name is provided, create a Table object from it.
            $tableObject = SyntaxFactory::createTable([$table]);
        } elseif (null === $table) {
            // If no table is provided for the column, use the main query's table.
            $tableObject = $this->getTable();
        }
        // $tableObject is now ?Table, which is the expected type for SyntaxFactory::createColumn

        $columnInstance = SyntaxFactory::createColumn([$column], $tableObject);
        $this->orderBy[] = new OrderBy($columnInstance, $direction);
        return $this;
    }

    public function getLimitCount(): ?int
    {
        return $this->limitCount;
    }

    public function getLimitStart(): ?int
    {
        return $this->limitStart;
    }

    public function setComment(string $comment): self
    {
        // Make each line of the comment prefixed with "--",
        // and remove any trailing whitespace.
        $processedComment = '-- ' . str_replace("\n", "\n-- ", \rtrim($comment));

        // Trim off any trailing "-- ", to ensure that the comment is valid.
        $this->comment = \rtrim($processedComment, '- ');

        if ($this->comment !== '') {
            $this->comment .= "\n";
        }
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
