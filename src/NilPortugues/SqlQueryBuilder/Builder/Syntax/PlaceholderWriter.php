<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/4/14
 * Time: 12:02 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\SqlQueryBuilder\Builder\Syntax;

/**
 * Class PlaceholderWriter
 * @package NilPortugues\SqlQueryBuilder\Builder
 */
class PlaceholderWriter
{
    /**
     * @var integer
     */
    protected $counter = 1;

    /**
     * @var array
     */
    protected $placeholders = array();

    /**
     * @return array
     */
    public function get()
    {
        return $this->placeholders;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->counter      = 1;
        $this->placeholders = array();

        return $this;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function add($value)
    {
        $placeholderKey                      = ':v' . $this->counter;
        $this->placeholders[$placeholderKey] = $this->setValidSqlValue($value);

        $this->counter++;

        return $placeholderKey;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function setValidSqlValue($value)
    {
        if (is_null($value) || (is_string($value) && empty($value))) {
            $value = $this->writeNull();
        }

        if (is_string($value)) {
            $value = $this->writeString($value);
        }

        if (is_bool($value)) {
            $value = $this->writeBoolean($value);
        }

        return $value;
    }

    /**
     * @return string
     */
    protected function writeNull()
    {
        return "NULL";
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeString($value)
    {
        return $value;
    }

    /**
     * @param boolean $value
     *
     * @return string
     */
    protected function writeBoolean($value)
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return ($value) ? "1" : "0";
    }
}
