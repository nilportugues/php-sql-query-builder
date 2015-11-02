<?php
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
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var bool
     */
    protected $view = false;

    /**
     * @param        $name
     * @param string $schema
     */
    public function __construct($name, $schema = null)
    {
        $this->name = $name;

        if (!is_null($schema)) {
            $this->schema = $schema;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @param bool $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return bool
     */
    public function isView()
    {
        return $this->view;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getCompleteName()
    {
        $alias = ($this->alias) ? " AS {$this->alias}" : '';
        $schema = ($this->schema) ? "{$this->schema}." : '';

        return $schema.$this->name.$alias;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param string
     * @param string $schema
     *
     * @return $this
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;

        return $this;
    }
}
