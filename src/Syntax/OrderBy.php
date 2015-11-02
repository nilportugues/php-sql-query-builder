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
 * Class OrderBy.
 */
class OrderBy
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * @var Column
     */
    protected $column;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var bool
     */
    protected $useAlias;

    /**
     * @param Column $column
     * @param string $direction
     */
    public function __construct(Column $column, $direction)
    {
        $this->setColumn($column);
        $this->setDirection($direction);
    }

    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setDirection($direction)
    {
        if (!in_array($direction, array(self::ASC, self::DESC))) {
            throw new \InvalidArgumentException(
                "Specified direction '$direction' is not allowed. Only ASC or DESC are allowed."
            );
        }
        $this->direction = $direction;

        return $this;
    }
}
