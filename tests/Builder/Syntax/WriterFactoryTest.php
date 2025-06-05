<?php

declare(strict_types=1);

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:47 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder\Syntax;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\ColumnWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\DeleteWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\InsertWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\IntersectWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\MinusWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\SelectWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\UnionAllWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\UnionWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\UpdateWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\WhereWriter;
use NilPortugues\Sql\QueryBuilder\Builder\Syntax\WriterFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class WriterFactoryTest.
 */
class WriterFactoryTest extends TestCase
{
    private PlaceholderWriter $placeholder;
    private GenericBuilder $writer;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->placeholder = new PlaceholderWriter();
    }

    /**
     * @test
     */
    public function itShouldCreateColumnWriter(): void
    {
        $writer = WriterFactory::createColumnWriter($this->writer, $this->placeholder);
        $this->assertInstanceOf(ColumnWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateWhereWriter(): void
    {
        $writer = WriterFactory::createWhereWriter($this->writer, $this->placeholder);
        $this->assertInstanceOf(WhereWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateSelectWriter(): void
    {
        $writer = WriterFactory::createSelectWriter($this->writer, $this->placeholder);
        $this->assertInstanceOf(SelectWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateInsertWriter(): void
    {
        $writer = WriterFactory::createInsertWriter($this->writer, $this->placeholder);
        $this->assertInstanceOf(InsertWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateUpdateWriter(): void
    {
        $writer = WriterFactory::createUpdateWriter($this->writer, $this->placeholder);
        $this->assertInstanceOf(UpdateWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateDeleteWriter(): void
    {
        $writer = WriterFactory::createDeleteWriter($this->writer, $this->placeholder);
        $this->assertInstanceOf(DeleteWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreatePlaceholderWriter(): void
    {
        $writer = WriterFactory::createPlaceholderWriter();
        $this->assertInstanceOf(PlaceholderWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateIntersectWriter(): void
    {
        $writer = WriterFactory::createIntersectWriter($this->writer);
        $this->assertInstanceOf(IntersectWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateMinusWriter(): void
    {
        $writer = WriterFactory::createMinusWriter($this->writer);
        $this->assertInstanceOf(MinusWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateUnion(): void
    {
        $writer = WriterFactory::createUnionWriter($this->writer);
        $this->assertInstanceOf(UnionWriter::class, $writer);
    }

    /**
     * @test
     */
    public function itShouldCreateUnionAll(): void
    {
        $writer = WriterFactory::createUnionAllWriter($this->writer);
        $this->assertInstanceOf(UnionAllWriter::class, $writer);
    }
}
