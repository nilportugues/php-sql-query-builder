<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:36 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Manipulation;

use NilPortugues\SqlQueryBuilder\Manipulation\Select;

/**
 * Class SelectTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Manipulation
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Select
     */
    private $query;
    /**
     *
     */
    protected function setUp()
    {
        $this->query  = new Select();
    }
    /**
     * @test
     */
    public function it_should_get_part_name()
    {
        $this->assertSame('SELECT', $this->query->partName());
    }
}
