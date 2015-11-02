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

use NilPortugues\Sql\QueryBuilder\Manipulation\Update;

/**
 * Class UpdateTest.
 */
class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Update
     */
    private $query;
    /**
     *
     */
    protected function setUp()
    {
        $this->query = new Update();
    }

    /**
     * @test
     */
    public function itShouldGetPartName()
    {
        $this->assertSame('UPDATE', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldReturnLimit1()
    {
        $this->query->limit(1);

        $this->assertSame(1, $this->query->getLimitStart());
    }

    /**
     * @test
     */
    public function itShouldReturnValues()
    {
        $values = ['user_id' => 1];

        $this->query->setValues($values);

        $this->assertSame($values, $this->query->getValues());
    }
}
