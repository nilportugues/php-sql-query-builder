<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 10:47 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax;

use NilPortugues\SqlQueryBuilder\Builder\GenericBuilder;
use NilPortugues\SqlQueryBuilder\Builder\Syntax\PlaceholderWriter;
use NilPortugues\SqlQueryBuilder\Builder\Syntax\WriterFactory;

/**
 * Class WriterFactoryTest
 * @package Tests\NilPortugues\SqlQueryBuilder\Builder\Syntax
 */
class WriterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PlaceholderWriter
     */
    private $placeholder;

    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     *
     */
    public function setUp()
    {
        $this->writer = new GenericBuilder();
        $this->placeholder = new PlaceholderWriter();
    }

    /**
     * @test
     */
    public function it_should_create_column_writer()
    {
        $writer = WriterFactory::createColumnWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\ColumnWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_where__writer()
    {
        $writer = WriterFactory::createWhereWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\WhereWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_select_writer()
    {
        $writer = WriterFactory::createSelectWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\SelectWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_insert_writer()
    {
        $writer = WriterFactory::createInsertWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\InsertWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_update_writer()
    {
        $writer = WriterFactory::createUpdateWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\UpdateWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_delete_writer()
    {
        $writer = WriterFactory::createDeleteWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\DeleteWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_placeholder_writer()
    {
        $writer = WriterFactory::createPlaceholderWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\PlaceholderWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_intersect_writer()
    {
        $writer = WriterFactory::createIntersectWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\IntersectWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_minus_writer()
    {
        $writer = WriterFactory::createMinusWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\MinusWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_union()
    {
        $writer = WriterFactory::createUnionWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\UnionWriter', get_class($writer));
    }

    /**
     * @test
     */
    public function it_should_create_union_all()
    {
        $writer = WriterFactory::createUnionAllWriter($this->writer, $this->placeholder);

        $this->assertSame('NilPortugues\SqlQueryBuilder\Builder\Syntax\UnionAllWriter', get_class($writer));
    }
}
