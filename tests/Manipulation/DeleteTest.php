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
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteTest.
 */
class DeleteTest extends TestCase
{
    // private GenericBuilder $writer; // Property $writer is unused.
    private Delete $query;

    protected function setUp(): void
    {
        $this->query = new Delete();
        $this->query->setBuilder(new GenericBuilder()); // Add builder for completeness
    }

    /**
     * @test
     */
    public function itShouldGetPartName(): void
    {
        $this->assertSame('DELETE', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldReturnLimit1(): void
    {
        $this->query->limit(1);
        $this->assertSame(1, $this->query->getLimitStart());
    }
}
