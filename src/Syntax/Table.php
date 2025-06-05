<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Syntax;

/**
 * Class Table.
 */
class Table
{
    protected ?string $alias = null;
    protected ?string $schema = null;
    protected bool $view = false;

    public function __construct(
        protected string $name,
        ?string $schema = null
    ) {
        if (null !== $schema) {
            $this->schema = $schema;
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setView(bool $view): self
    {
        $this->view = $view;
        return $this;
    }

    public function isView(): bool
    {
        return $this->view;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getCompleteName(): string
    {
        $aliasString = ($this->alias !== null && $this->alias !== '') ? " AS {$this->alias}" : '';
        $schemaString = ($this->schema !== null && $this->schema !== '') ? "{$this->schema}." : '';
        return $schemaString . $this->name . $aliasString;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }

    public function getSchema(): ?string
    {
        return $this->schema;
    }

    public function setSchema(?string $schema): self
    {
        $this->schema = $schema;
        return $this;
    }
}
