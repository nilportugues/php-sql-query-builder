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
use NilPortugues\SqlQueryBuilder\Manipulation\Union;

/**
 * Class UnionWriter
 * @package NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class UnionWriter
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
     * @param Union $intersect
     *
     * @return string
     */
    public function writeUnion(Union $intersect)
    {
        $unionSelects = array();

        foreach ($intersect->getUnions() as $select) {
            $unionSelects[] = $this->writer->write($select);
        }

        return implode("\n".Union::UNION."\n", $unionSelects);
    }
}
