<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:37 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;

/**
 * Class InsertTest.
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
        $this->query = new Insert();
    }

    /**
     * @test
     */
    public function itShouldGetPartName()
    {
        $this->assertSame('INSERT', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldSetValues()
    {
        $values = ['user_id' => 1, 'username' => 'nilportugues'];

        $this->query->setValues($values);

        $this->assertSame($values, $this->query->getValues());
    }

    /**
     * @test
     */
    public function itShouldGetColumns()
    {
        $values = ['user_id' => 1, 'username' => 'nilportugues'];

        $this->query->setValues($values);

        $columns = $this->query->getColumns();

        $this->assertInstanceOf('NilPortugues\Sql\QueryBuilder\Syntax\Column', $columns[0]);
    }
}
