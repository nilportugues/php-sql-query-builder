<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/4/14
 * Time: 12:02 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Builder\Syntax;

/**
 * Class PlaceholderWriter.
 */
class PlaceholderWriter
{
    protected int $counter = 1;

    /** @var array<string, mixed> */
    protected array $placeholders = [];

    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return $this->placeholders;
    }

    public function reset(): self
    {
        $this->counter = 1;
        $this->placeholders = [];

        return $this;
    }

    public function add(mixed $value): string
    {
        $placeholderKey = ':v' . $this->counter;
        $this->placeholders[$placeholderKey] = $this->setValidSqlValue($value);

        $this->counter++;

        return $placeholderKey;
    }

    protected function setValidSqlValue(mixed $value): mixed
    {
        $value = $this->writeNullSqlString($value);
        // If already string 'NULL', further checks might not be needed or could be adjusted
        if ($value === 'NULL') {
            return $value;
        }
        $value = $this->writeStringAsSqlString($value);
        $value = $this->writeBooleanSqlString($value);

        return $value;
    }

    protected function writeNullSqlString(mixed $value): mixed
    {
        if ($value === null || ($value === '')) { // Empty string is treated as NULL by some DBs or ORMs
            return $this->writeNull();
        }

        return $value;
    }

    protected function writeNull(): string
    {
        return 'NULL';
    }

    protected function writeStringAsSqlString(mixed $value): mixed
    {
        if (\is_string($value)) {
            return $this->writeString($value);
        }

        return $value;
    }

    protected function writeString(string $value): string
    {
        // This method seems to be a pass-through.
        // Actual SQL string escaping should happen in the database adapter/driver,
        // or this is where it would be implemented if needed at this stage.
        return $value;
    }

    protected function writeBooleanSqlString(mixed $value): mixed
    {
        if (\is_bool($value)) {
            return $this->writeBoolean($value);
        }

        return $value;
    }

    protected function writeBoolean(bool $value): string
    {
        return $value ? '1' : '0';
    }
}
