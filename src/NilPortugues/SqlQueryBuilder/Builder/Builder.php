<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\SqlQueryBuilder\Builder;

use NilPortugues\SqlQueryBuilder\Manipulation\Query;

/**
 * Interface Builder
 * @package NilPortugues\SqlQueryBuilder\Builder
 */
interface Builder
{
    /**
     * @param Query $query
     *
     * @return string
     */
    public function write(Query $query);
}
