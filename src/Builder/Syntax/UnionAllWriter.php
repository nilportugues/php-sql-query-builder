<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:15 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Manipulation\UnionAll;

/**
 * Class UnionAllWriter
 * @package NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class UnionAllWriter
{
    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     * @param GenericBuilder $writer
     */
    public function __construct(GenericBuilder $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param UnionAll $intersect
     *
     * @return string
     */
    public function write(UnionAll $intersect)
    {
        $unionSelects = array();

        foreach ($intersect->getUnions() as $select) {
            $unionSelects[] = $this->writer->write($select);
        }

        return implode("\n".UnionAll::UNION_ALL."\n", $unionSelects);
    }
}
