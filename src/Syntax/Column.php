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

/**
 * Class Column.
 */
class Column implements QueryPartInterface
{
    final public const ALL = '*';

    protected ?Table $table = null;
    protected ?string $alias = null;

    public function __construct(
        protected string $name,
        ?string $tableName,
        string $alias = ''
    ) {
        $this->setTable($tableName);
        $this->setAlias($alias);
    }

    public function partName(): string
    {
        return 'COLUMN';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?string $tableName): self
    {
        if (null === $tableName || $tableName === '') {
            $this->table = null;
        } else {
            $this->table = SyntaxFactory::createTable([$tableName]);
        }
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @throws QueryException
     */
    public function setAlias(?string $alias): self
    {
        if (null === $alias || $alias === '') {
            $this->alias = null;
            return $this;
        }

        if ($this->isAll()) {
            throw new QueryException("Can't use alias because column name is ALL (*)");
        }

        $this->alias = $alias;
        return $this;
    }

    /**
     * Check whether column name is '*' or not.
     */
    public function isAll(): bool
    {
        return $this->name === self::ALL;
    }
}
