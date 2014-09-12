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

use NilPortugues\SqlQueryBuilder\Manipulation\Update;

/**
 * Class UpdateTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
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
        $this->query  = new Update();
    }

    /**
     * @test
     */
    public function it_should_get_part_name()
    {
        $this->assertSame('UPDATE', $this->query->partName());
    }

    /**
     * @test
     */
    public function it_should_return_limit_1()
    {
        $this->query->limit(1);

        $this->assertSame(1, $this->query->getLimitStart());
    }

    /**
     * @test
     */
    public function it_should_return_values()
    {
        $values = ['user_id' => 1];

        $this->query->setValues($values);

        $this->assertSame($values, $this->query->getValues());
    }
}
