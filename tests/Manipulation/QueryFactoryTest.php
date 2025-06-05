<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/16/14
 * Time: 8:50 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Manipulation;

use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Intersect; // Added for completeness, though not in original test
use NilPortugues\Sql\QueryBuilder\Manipulation\Minus;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryFactory;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryInterface;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Union;
use NilPortugues\Sql\QueryBuilder\Manipulation\UnionAll;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryFactoryTest.
 */
class QueryFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateSelectObject(): void
    {
        $this->assertInstanceOf(Select::class, QueryFactory::createSelect());
    }

    /**
     * @test
     */
    public function itShouldCreateInsertObject(): void
    {
        $this->assertInstanceOf(Insert::class, QueryFactory::createInsert());
    }

    /**
     * @test
     */
    public function itShouldCreateUpdateObject(): void
    {
        $this->assertInstanceOf(Update::class, QueryFactory::createUpdate());
    }

    /**
     * @test
     */
    public function itShouldCreateDeleteObject(): void
    {
        $this->assertInstanceOf(Delete::class, QueryFactory::createDelete());
    }

    /**
     * @test
     */
    public function itShouldCreateMinusObject(): void
    {
        // These Select objects don't need a builder as they are just constructor arguments here.
        $this->assertInstanceOf(Minus::class, QueryFactory::createMinus(new Select('table1'), new Select('table2')));
    }

    /**
     * @test
     */
    public function itShouldCreateUnionObject(): void
    {
        $this->assertInstanceOf(Union::class, QueryFactory::createUnion());
    }

    /**
     * @test
     */
    public function itShouldCreateUnionAllObject(): void
    {
        $this->assertInstanceOf(UnionAll::class, QueryFactory::createUnionAll());
    }

    /**
     * @test
     */
    public function itShouldCreateIntersectObject(): void // Added test for createIntersect
    {
        $this->assertInstanceOf(Intersect::class, QueryFactory::createIntersect());
    }

    /**
     * @test
     */
    public function itShouldCreateWhereObject(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $this->assertInstanceOf(Where::class, QueryFactory::createWhere($query));
    }
}
