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
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * Interface QueryInterface.
 */
interface QueryInterface
{
    public function partName(): string;

    public function getTable(): ?Table;

    public function getWhere(): ?Where;

    public function where(): Where; // Or specify where(string $operator = 'AND'): Where; for consistency

    public function setBuilder(BuilderInterface $builder): self;

    public function getBuilder(): BuilderInterface;

    public function getSql(bool $formatted = false): string;

    public function __toString(): string;
}
