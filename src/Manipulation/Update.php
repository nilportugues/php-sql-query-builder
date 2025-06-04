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

/**
 * Class Update.
 */
class Update extends AbstractCreationalQuery
{
    // Properties $limitStart and $orderBy are inherited from AbstractBaseQuery.
    // No need to redeclare them here if visibility and type are compatible.
    // AbstractBaseQuery has:
    // protected ?int $limitStart = null;
    // protected array $orderBy = []; // which is array<OrderBy>

    public function partName(): string
    {
        return 'UPDATE';
    }

    // getLimitStart() is inherited from AbstractBaseQuery and returns ?int.
    // This override can be removed if it provides no different logic or typing.
    // Parent is already ?int.
    public function getLimitStart(): ?int
    {
        return $this->limitStart;
    }

    public function limit(int $start): self
    {
        // This sets the $limitStart property inherited from AbstractBaseQuery.
        // The AbstractBaseWriter::writeLimitCondition only uses getLimitStart(),
        // so this will effectively produce "LIMIT N".
        $this->limitStart = $start;
        return $this;
    }

    // Note: The orderBy method is inherited from AbstractBaseQuery.
    // If Update queries have specific orderBy behavior, it would be overridden here.
    // The $orderBy property redeclaration was removed as it's handled by parent.
}
