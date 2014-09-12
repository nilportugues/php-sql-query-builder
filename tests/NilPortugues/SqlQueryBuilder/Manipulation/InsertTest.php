<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:37 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Manipulation\Insert;

/**
 * Class InsertTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
 */
class InsertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Insert
     */
    private $query;
    /**
     *
     */
    protected function setUp()
    {
        $this->query  = new Insert();
    }

    /**
     * @test
     */
    public function it_should_get_part_name()
    {
        $this->assertSame('INSERT', $this->query->partName());
    }

    /**
     * @test
     */
    public function it_should_set_values()
    {
        $values = ['user_id' => 1, 'username' => 'nilportugues'];

        $this->query->setValues($values);

        $this->assertSame($values, $this->query->getValues());
    }

    /**
     * @test
     */
    public function it_should_get_columns()
    {
        $values = ['user_id' => 1, 'username' => 'nilportugues'];

        $this->query->setValues($values);

        $columns = $this->query->getColumns();

        $this->assertInstanceOf('NilPortugues\SqlQueryBuilder\Syntax\Column', $columns[0]);
    }
}
