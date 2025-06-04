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
 * Class Delete.
 */
class Delete extends AbstractBaseQuery
{
    // The $limitStart property is inherited from AbstractBaseQuery (?int)
    // No need to redeclare unless changing visibility or type, which is not the case.

    public function __construct(?string $table = null)
    {
        if (null !== $table) {
            $this->setTable($table);
        }
    }

    public function partName(): string
    {
        return 'DELETE';
    }

    // getLimitStart() is inherited from AbstractBaseQuery and returns ?int.
    // This local override might be intended if the logic for Delete's limit start was different,
    // but it just returns the property. If the property is the same as parent,
    // this override is not strictly necessary unless for specific typing, but parent is already ?int.
    // For now, keeping it as it might be a specific design choice, but ensuring return type is compatible.
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
}
