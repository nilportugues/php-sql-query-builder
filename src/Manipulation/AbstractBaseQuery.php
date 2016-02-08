<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Syntax\QueryPartInterface;
use NilPortugues\Sql\QueryBuilder\Syntax\SyntaxFactory;
use NilPortugues\Sql\QueryBuilder\Syntax\Table;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
// Builder injects itself into query for convestion to SQL string.
use NilPortugues\Sql\QueryBuilder\Builder\BuilderInterface;

/**
 * Class AbstractBaseQuery.
 */
abstract class AbstractBaseQuery implements QueryInterface, QueryPartInterface
{
    /**
     * @var string
     */
    protected $comment = '';

    /**
     * @var \NilPortugues\Sql\QueryBuilder\Builder\BuilderInterface
     */
    protected $builder;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $whereOperator = 'AND';

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var int
     */
    protected $limitStart;

    /**
     * @var int
     */
    protected $limitCount;

    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * @return Where
     */
    protected function filter()
    {
        if (!isset($this->where)) {
            $this->where = QueryFactory::createWhere($this);
        }

        return $this->where;
    }

    /**
     * Stores the builder that created this query.
     *
     * @param BuilderInterface $builder
     *
     * @return $this
     */
    final public function setBuilder(BuilderInterface $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @return BuilderInterface
     *
     * @throws \RuntimeException when builder has not been injected
     */
    final public function getBuilder()
    {
        if (!$this->builder) {
            throw new \RuntimeException('Query builder has not been injected with setBuilder');
        }

        return $this->builder;
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getSql();
        } catch (\Exception $e) {
            return \sprintf('[%s] %s', \get_class($e), $e->getMessage());
        }
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     * Optionally can return the SQL with formatted structure.
     *
     * @param bool $formatted
     *
     * @return string
     */
    public function getSql($formatted = false)
    {
        if ($formatted) {
            return $this->getBuilder()->writeFormatted($this);
        }

        return $this->getBuilder()->write($this);
    }

    /**
     * @return string
     */
    abstract public function partName();

    /**
     * @return Where
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        $newTable = array($this->table);

        return \is_null($this->table) ? null : SyntaxFactory::createTable($newTable);
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    /**
     * @param string $whereOperator
     *
     * @return Where
     */
    public function where($whereOperator = 'AND')
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        $this->where->conjunction($whereOperator);

        return $this->where;
    }

    /**
     * @return string
     */
    public function getWhereOperator()
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        return $this->where->getConjunction();
    }

    /**
     * @param string $column
     * @param string $direction
     * @param null   $table
     *
     * @return $this
     */
    public function orderBy($column, $direction = OrderBy::ASC, $table = null)
    {
        $newColumn = array($column);
        $column = SyntaxFactory::createColumn($newColumn, \is_null($table) ? $this->getTable() : $table);
        $this->orderBy[] = new OrderBy($column, $direction);

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitCount()
    {
        return $this->limitCount;
    }

    /**
     * @return int
     */
    public function getLimitStart()
    {
        return $this->limitStart;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        // Make each line of the comment prefixed with "--",
        // and remove any trailing whitespace.
        $comment = '-- '.str_replace("\n", "\n-- ", \rtrim($comment));

        // Trim off any trailing "-- ", to ensure that the comment is valid.
        $this->comment = \rtrim($comment, '- ');

        if ($this->comment) {
            $this->comment .= "\n";
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
