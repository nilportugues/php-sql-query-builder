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
use PHPUnit\Framework\TestCase;

/**
 * Class InsertTest.
 */
class InsertTest extends TestCase
{
    /**
     * @var Insert
     */
    private $query;
    protected function setUp(): void
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

    /**
     * @test
     */
    public function itShouldSetNullableValues()
    {
        $values = ['user_id' => 1, 'description' => null, 'isVisible' => false];

        $this->query->setValues($values);

        $this->assertSame($values, $this->query->getValues());
    }
}
