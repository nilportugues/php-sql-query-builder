<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\SqlQueryBuilder\Manipulation;

/**
 * Interface Query
 * @package NilPortugues\SqlQueryBuilder\Manipulation
 */
interface Query
{
    /**
     * @return \NilPortugues\SqlQueryBuilder\Syntax\Table
     */
    public function getTable();

    /**
     * @return \NilPortugues\SqlQueryBuilder\Syntax\Where
     */
    public function getWhere();

    /**
     * @return \NilPortugues\SqlQueryBuilder\Syntax\Where
     */
    public function where();
}
