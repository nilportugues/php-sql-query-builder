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
 * Class Delete
 * @package NilPortugues\SqlQueryBuilder\Manipulation
 */
class Delete extends BaseQuery
{

    /**
     * @var int
     */
    protected $limitStart;

    /**
     * @return string
     */
    public function partName()
    {
        return 'DELETE';
    }

    /**
     * @return int
     */
    public function getLimitStart()
    {
        return $this->limitStart;
    }

    /**
     * @param integer $start
     *
     * @return $this
     */
    public function limit($start)
    {
        $this->limitStart = $start;

        return $this;
    }
}
