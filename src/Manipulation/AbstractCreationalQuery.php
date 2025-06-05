<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/24/14
 * Time: 12:30 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

/**
 * Class AbstractCreationalQuery.
 */
abstract class AbstractCreationalQuery extends AbstractBaseQuery
{
    /** @var array<mixed> */
    protected array $values = [];

    /**
     * @param string|null $table
     * @param array<mixed>|null $values
     */
    public function __construct(?string $table = null, ?array $values = null)
    {
        if (null !== $table) {
            $this->setTable($table);
        }

        if (null !== $values && !empty($values)) { // ensure $values is not null before !empty
            $this->setValues($values);
        }
    }

    /**
     * @return array<mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array<mixed> $values
     */
    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }
}
