<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:11 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Builder\BuilderInterface;
use NilPortugues\Sql\QueryBuilder\Syntax\QueryPartInterface;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Class Minus.
 */
class Minus implements QueryInterface, QueryPartInterface
{
    final public const MINUS = 'MINUS';
    protected ?BuilderInterface $builder = null;

    public function __construct(
        protected Select $first,
        protected Select $second
    ) {
    }

    public function partName(): string
    {
        return self::MINUS;
    }

    public function getFirst(): Select
    {
        return $this->first;
    }

    public function getSecond(): Select
    {
        return $this->second;
    }

    /**
     * @throws QueryException
     */
    public function getTable(): ?Table
    {
        throw new QueryException(\sprintf('%s does not support tables', $this->partName()));
    }

    /**
     * @throws QueryException
     */
    public function getWhere(): ?Where
    {
        throw new QueryException(\sprintf('%s does not support WHERE.', $this->partName()));
    }

    /**
     * @throws QueryException
     */
    public function where(): Where
    {
        throw new QueryException(\sprintf('%s does not support the WHERE statement.', $this->partName()));
    }

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
            throw new \RuntimeException('Query builder has not been injected with setBuilder for Minus query.');
        }
        return $this->builder;
    }

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
}
