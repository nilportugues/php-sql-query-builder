<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 1:37 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder; // For setBuilder
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use PHPUnit\Framework\TestCase;

/**
 * Class UpdateTest.
 */
class UpdateTest extends TestCase
{
    private Update $query;

    protected function setUp(): void
    {
        $this->query = new Update();
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('UPDATE', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldReturnLimit1(): void
    {
        $this->query->limit(1);
        $this->assertSame(1, $this->query->getLimitStart());
    }

    /**
     * @test
     */
    public function itShouldReturnValues(): void
    {
        $values = ['user_id' => 1];
        $this->query->setValues($values);
        $this->assertSame($values, $this->query->getValues());
    }

    /**
     * @test
     */
    public function itShouldSetNullableValues(): void
    {
        $values = ['user_id' => 1, 'description' => null, 'isVisible' => false];
        $this->query->setValues($values);
        $this->assertSame($values, $this->query->getValues());
    }
}
