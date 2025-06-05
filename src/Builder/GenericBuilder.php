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

namespace NilPortugues\Sql\QueryBuilder\Builder;

use NilPortugues\Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\SelectWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\WhereWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\WriterFactory;
use NilPortugues\Sql\QueryBuilder\Manipulation\AbstractBaseQuery;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Intersect;
use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryFactory;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryInterface;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Union;
use NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;

/**
 * Class Generic.
 */
class GenericBuilder implements BuilderInterface
{
    protected PlaceholderWriter $placeholderWriter;
    protected ?WhereWriter $whereWriter = null;
    protected ?\NilPortugues\Sql\QueryFormatter\Formatter $sqlFormatter = null;
    protected string $sqlFormatterClass = 'NilPortugues\Sql\QueryFormatter\Formatter';

    /** @var array<string, string> */
    protected array $queryWriterArray = [
        'SELECT' => WriterFactory::class . '::createSelectWriter',
        'INSERT' => WriterFactory::class . '::createInsertWriter',
        'UPDATE' => WriterFactory::class . '::createUpdateWriter',
        'DELETE' => WriterFactory::class . '::createDeleteWriter',
        'INTERSECT' => WriterFactory::class . '::createIntersectWriter',
        'MINUS' => WriterFactory::class . '::createMinusWriter',
        'UNION' => WriterFactory::class . '::createUnionWriter',
        'UNION ALL' => WriterFactory::class . '::createUnionAllWriter',
    ];

    /** @var array<string, object|null> */
    protected array $queryWriterInstances = [
        'SELECT' => null,
        'INSERT' => null,
        'UPDATE' => null,
        'DELETE' => null,
        'INTERSECT' => null,
        'MINUS' => null,
        'UNION' => null,
        'UNION ALL' => null,
    ];

    /**
     * Creates writers.
     */
    public function __construct()
    {
        $this->placeholderWriter = WriterFactory::createPlaceholderWriter();
    }

    /**
     * @param array<string>|null $columns
     */
    public function select(?string $table = null, ?array $columns = null): Select
    {
        return $this->injectBuilder(QueryFactory::createSelect($table, $columns));
    }

    /**
     * @template T of AbstractBaseQuery
     * @param T $query
     * @return T
     */
    protected function injectBuilder(AbstractBaseQuery $query): AbstractBaseQuery
    {
        return $query->setBuilder($this);
    }

    /**
     * @param array<mixed>|null $values
     */
    public function insert(?string $table = null, ?array $values = null): Insert
    {
        return $this->injectBuilder(QueryFactory::createInsert($table, $values));
    }

    /**
     * @param array<mixed>|null $values
     */
    public function update(?string $table = null, ?array $values = null): Update
    {
        return $this->injectBuilder(QueryFactory::createUpdate($table, $values));
    }

    public function delete(?string $table = null): Delete
    {
        return $this->injectBuilder(QueryFactory::createDelete($table));
    }

    public function intersect(): Intersect
    {
        return QueryFactory::createIntersect()->setBuilder($this);
    }

    public function union(): Union
    {
        return QueryFactory::createUnion()->setBuilder($this);
    }

    public function unionAll(): UnionAll
    {
        return QueryFactory::createUnionAll()->setBuilder($this);
    }

    public function minus(Select $first, Select $second): Minus
    {
        return QueryFactory::createMinus($first, $second)->setBuilder($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->placeholderWriter->get();
    }

    /**
     * Returns a SQL string in a readable human-friendly format.
     * @throws \ReflectionException
     */
    public function writeFormatted(QueryInterface $query): string
    {
        if (null === $this->sqlFormatter) {
            $this->sqlFormatter = (new \ReflectionClass($this->sqlFormatterClass))->newInstance();
        }
        return $this->sqlFormatter->format($this->write($query));
    }

    /**
     * @throws \RuntimeException|\ReflectionException
     */
    public function write(QueryInterface $query, bool $resetPlaceholders = true): string
    {
        if ($resetPlaceholders) {
            $this->placeholderWriter->reset();
        }

        $queryPart = $query->partName();

        if (!empty($this->queryWriterArray[$queryPart])) {
            $this->createQueryObject($queryPart);

            /** @var SelectWriter|object|null $writerInstance */
            $writerInstance = $this->queryWriterInstances[$queryPart];
            if ($writerInstance && method_exists($writerInstance, 'write')) {
                return $writerInstance->write($query);
            }
        }

        throw new \RuntimeException('Query builder part not defined or writer instance/method is invalid.');
    }

    /**
     * @throws \ReflectionException
     */
    public function writeJoin(Select $select): string
    {
        if (null === $this->whereWriter) {
            $this->whereWriter = WriterFactory::createWhereWriter($this, $this->placeholderWriter);
        }

        $sql = ($select->getJoinType()) ? "{$select->getJoinType()} " : '';
        $sql .= 'JOIN ';
        $sql .= $this->writeTableWithAlias($select->getTable());
        $sql .= ' ON ';
        $sql .= $this->whereWriter->writeWhere($select->getJoinCondition());

        return $sql;
    }

    public function writeTableWithAlias(?Table $table): string
    {
        if ($table === null) {
            return '';
        }
        $alias = $table->getAlias();
        $aliasString = ($alias !== null && $alias !== '') ? " AS {$this->writeTableAlias($alias)}" : '';
        $schema = ($table->getSchema()) ? "{$table->getSchema()}." : '';

        return $schema . $this->writeTableName($table) . $aliasString;
    }

    public function writeTableAlias(string $alias): string
    {
        return $alias; // Usually, aliases are not quoted unless they are keywords or contain special chars.
    }

    public function writeTableName(Table $table): string
    {
        return $table->getName(); // Table names might need quoting depending on DB if they are keywords or contain special chars.
    }

    public function writeColumnAlias(string $alias): string
    {
        // ANSI SQL standard for quoting identifiers is double quotes.
        return '"' . str_replace('"', '""', $alias) . '"';
    }

    public function writeTable(Table $table): string
    {
        $schema = ($table->getSchema()) ? "{$table->getSchema()}." : '';
        return $schema . $this->writeTableName($table);
    }

    /**
     * @param array<mixed> $values
     * @return array<string>
     */
    public function writeValues(array $values): array
    {
        $writtenValues = [];
        foreach ($values as $value) {
            $writtenValues[] = $this->writePlaceholderValue($value);
        }
        return $writtenValues;
    }

    public function writePlaceholderValue(mixed $value): string
    {
        return $this->placeholderWriter->add($value);
    }

    public function writeConjunction(string $operator): string
    {
        return ' ' . $operator . ' ';
    }

    public function writeIsNull(): string
    {
        return ' IS NULL';
    }

    public function writeIsNotNull(): string
    {
        return ' IS NOT NULL';
    }

    public function writeColumnName(Column $column): string
    {
        $name = $column->getName();

        if ($name === Column::ALL) {
            return $this->writeColumnAll();
        }
        // Column names might need quoting depending on DB if they are keywords or contain special chars.
        return $name;
    }

    protected function writeColumnAll(): string
    {
        return '*';
    }

    /**
     * @throws \ReflectionException
     */
    protected function createQueryObject(string $queryPart): void
    {
        if (null === $this->queryWriterInstances[$queryPart]) {
            $writerFactoryMethod = $this->queryWriterArray[$queryPart];
            // $writerFactoryMethod is already known to be a string from array<string, string>
            if (str_contains($writerFactoryMethod, '::')) {
                /** @var callable $callable */
                $callable = explode('::', $writerFactoryMethod);
                $this->queryWriterInstances[$queryPart] = \call_user_func_array(
                    $callable,
                    [$this, $this->placeholderWriter]
                );
            } else {
                throw new \RuntimeException("Invalid factory method definition for query part: {$queryPart}");
            }
        }
    }
}
