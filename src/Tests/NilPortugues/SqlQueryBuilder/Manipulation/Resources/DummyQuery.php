<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:58 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Manipulation\Resources;

use NilPortugues\SqlQueryBuilder\Manipulation\BaseQuery;

/**
 * Class DummyQuery
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation\Resources
 */
class DummyQuery extends BaseQuery
{
    /**
     * @return string
     */
    public function partName()
    {
        return 'DUMMY';
    }
}
