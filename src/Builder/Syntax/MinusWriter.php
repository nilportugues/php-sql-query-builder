<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:15 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;

/**
 * Class MinusWriter.
 */
class MinusWriter
{
    public function __construct(protected GenericBuilder $writer)
    {
    }

    /**
     * @throws \ReflectionException
     */
    public function write(Minus $minus): string
    {
        $first = $this->writer->write($minus->getFirst());
        $second = $this->writer->write($minus->getSecond());

        return $first . "\n" . Minus::MINUS . "\n" . $second;
    }
}
