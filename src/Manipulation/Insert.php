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

use NilPortugues\Sql\QueryBuilder\Syntax\Column; // Import Column for return type
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;

/**
 * Class Insert.
 */
class Insert extends AbstractCreationalQuery
{
    public function partName(): string
    {
        return 'INSERT';
    }

    /**
     * @return array<Column>
     */
    public function getColumns(): array
    {
        /** @var array<string> $columnNames */
        $columnNames = \array_keys($this->values);

        return SyntaxFactory::createColumns($columnNames, $this->getTable());
    }
}
