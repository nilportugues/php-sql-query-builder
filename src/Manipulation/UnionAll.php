<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:11 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

/**
 * Class UnionAll.
 */
class UnionAll extends AbstractSetQuery
{
    const UNION_ALL = 'UNION ALL';

    /**
     * @return string
     */
    public function partName()
    {
        return 'UNION ALL';
    }
}
