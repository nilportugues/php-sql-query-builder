<?php

declare(strict_types=1);
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/4/14
 * Time: 12:40 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Sql\QueryBuilder\Builder;

use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use PHPUnit\Framework\TestCase;

/**
 * Class MySqlBuilderTest.
 */
class MySqlBuilderTest extends TestCase
{
    private MySqlBuilder $writer;

    protected function setUp(): void
    {
        $this->writer = new MySqlBuilder();
    }

    protected function tearDown(): void
    {
        // $this->writer will be automatically garbage collected.
    }

    /**
     * @test
     */
    public function itShouldWrapTableNames(): void
    {
        $query = new Select('user');
        $query->setBuilder($this->writer); // Set builder

        $expected = 'SELECT `user`.* FROM `user`';
        $this->assertSame($expected, $this->writer->write($query));
    }

    /**
     * @test
     */
    public function itShouldWrapColumnNames(): void
    {
        $query = new Select('user', ['user_id', 'name']);
        $query->setBuilder($this->writer); // Set builder

        $expected = 'SELECT `user`.`user_id`, `user`.`name` FROM `user`';
        $this->assertSame($expected, $this->writer->write($query));
    }

    /**
     * @test
     */
    public function itShouldWrapColumnAlias(): void
    {
        $query = new Select('user', ['userId' => 'user_id', 'name' => 'name']);
        $query->setBuilder($this->writer); // Set builder

        $expected = 'SELECT `user`.`user_id` AS `userId`, `user`.`name` AS `name` FROM `user`';
        $this->assertSame($expected, $this->writer->write($query));
    }
}
