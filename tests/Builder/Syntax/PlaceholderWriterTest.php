<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:46 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class PlaceholderWriterTest.
 */
class PlaceholderWriterTest extends TestCase
{
    private PlaceholderWriter $writer;

    protected function setUp(): void
    {
        $this->writer = new PlaceholderWriter();
    }

    /**
     * @test
     */
    public function itShouldAddValueAndReturnPlaceholder(): void
    {
        $result = $this->writer->add(1);
        $this->assertEquals(':v1', $result);
    }

    /**
     * @test
     */
    public function itShouldAddValueAndGetReturnsArrayHoldingPlaceholderData(): void
    {
        $this->writer->add(1);
        $this->assertEquals([':v1' => 1], $this->writer->get());
    }

    /**
     * @test
     */
    public function itShouldTranslatePhpNullToSqlNullValue(): void
    {
        $this->writer->add('');
        $this->writer->add(null);

        $this->assertEquals([':v1' => 'NULL', ':v2' => 'NULL'], $this->writer->get());
    }

    /**
     * @test
     */
    public function itShouldTranslatePhpBoolToSqlBoolValue(): void
    {
        $this->writer->add(true);
        $this->writer->add(false);

        $this->assertEquals([':v1' => '1', ':v2' => '0'], $this->writer->get()); // Values are strings '1' and '0'
    }
}
