<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:15 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Manipulation\Union;

/**
 * Class UnionWriter.
 */
class UnionWriter extends AbstractSetWriter
{
    public function write(Union $union): string
    {
        return $this->abstractWrite($union, 'getUnions', Union::UNION);
    }
}
