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

/**
 * Class OrderBy.
 */
class OrderBy
{
    final public const ASC = 'ASC';
    final public const DESC = 'DESC';

    protected bool $useAlias; // This property is unused in the provided code.

    public function __construct(
        protected Column $column,
        protected string $direction
    ) {
        $this->setDirection($direction); // Validate direction
        // $useAlias is not initialized in constructor, defaults to PHP uninitialized state for bool (effectively false)
        // If it were to be used, it should be initialized, e.g., $this->useAlias = $useAliasParam;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    public function setColumn(Column $column): self
    {
        $this->column = $column;
        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setDirection(string $direction): self
    {
        if (!\in_array($direction, [self::ASC, self::DESC], true)) {
            throw new \InvalidArgumentException(
                "Specified direction '{$direction}' is not allowed. Only ASC or DESC are allowed."
            );
        }
        $this->direction = $direction;
        return $this;
    }

    // Example of how useAlias might be used if it were functional
    // public function useAlias(bool $use = true): self
    // {
    //     $this->useAlias = $use;
    //     return $this;
    // }

    // public function isUsingAlias(): bool
    // {
    //     return $this->useAlias;
    // }
}
