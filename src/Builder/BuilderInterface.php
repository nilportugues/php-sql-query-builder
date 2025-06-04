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

use NilPortugues\Sql\QueryBuilder\Manipulation\QueryInterface;

/**
 * Interface BuilderInterface.
 */
interface BuilderInterface
{
    public function write(QueryInterface $query): string;

    public function writeFormatted(QueryInterface $query): string;

    public function writeColumnAlias(string $alias): string;
}
