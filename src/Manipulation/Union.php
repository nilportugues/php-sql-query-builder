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
 * Class Union.
 */
class Union extends AbstractSetQuery
{
    const UNION = 'UNION';

    /**
     * @return string
     */
    public function partName()
    {
        return 'UNION';
    }
}
