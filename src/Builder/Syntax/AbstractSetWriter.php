<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/24/14
 * Time: 12:55 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Syntax\QueryPartInterface;

/**
 * Class AbstractSetWriter.
 */
abstract class AbstractSetWriter
{
    public function __construct(protected GenericBuilder $writer)
    {
    }

    /**
     * @param QueryPartInterface $setClass
     * @param string $setOperation
     * @param string $glue
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    protected function abstractWrite(QueryPartInterface $setClass, string $setOperation, string $glue): string
    {
        $selects = [];

        /** @var \NilPortugues\Sql\QueryBuilder\Manipulation\QueryInterface $select */
        foreach ($setClass->$setOperation() as $select) {
            $selects[] = $this->writer->write($select, false);
        }

        return \implode("\n".$glue."\n", $selects);
    }
}
