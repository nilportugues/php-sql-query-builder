<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/24/14
 * Time: 12:30 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\QueryPartInterface;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
/**
 * Class AbstractSetQuery.
 */
use NilPortugues\Sql\QueryBuilder\Builder\BuilderInterface;

abstract class AbstractSetQuery implements QueryInterface, QueryPartInterface
{
    /** @var array<Select> */
    protected array $union = [];
    protected ?BuilderInterface $builder = null;

    final public function setBuilder(BuilderInterface $builder): self
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @throws \RuntimeException
     */
    final public function getBuilder(): BuilderInterface
    {
        if (null === $this->builder) {
            throw new \RuntimeException('Query builder has not been injected with setBuilder for this set query.');
        }
        return $this->builder;
    }

    /**
     * @throws \ReflectionException
     */
    public function getSql(bool $formatted = false): string
    {
        if ($formatted) {
            return $this->getBuilder()->writeFormatted($this);
        }
        return $this->getBuilder()->write($this);
    }

    public function __toString(): string
    {
        try {
            return $this->getSql();
        } catch (\Exception $e) {
            return \sprintf('[%s] %s', \get_class($e), $e->getMessage());
        }
    }

    public function add(Select $select): self
    {
        $this->union[] = $select;
        return $this;
    }

    /**
     * @return array<Select>
     */
    public function getUnions(): array
    {
        return $this->union;
    }

    /**
     * @throws QueryException
     */
    public function getTable(): ?Table // As per AbstractBaseQuery's implementation of QueryPartInterface
    {
        throw new QueryException(
            \sprintf('%s does not support tables', $this->partName())
        );
    }

    /**
     * @throws QueryException
     */
    public function getWhere(): ?Where // As per AbstractBaseQuery's implementation of QueryPartInterface
    {
        throw new QueryException(
            \sprintf('%s does not support WHERE.', $this->partName())
        );
    }

    /**
     * @throws QueryException
     */
    public function where(): Where // As per AbstractBaseQuery's implementation of QueryPartInterface
    {
        throw new QueryException(
            \sprintf('%s does not support the WHERE statement.', $this->partName())
        );
    }
}
