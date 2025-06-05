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

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryException;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Syntax\Column;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use PHPUnit\Framework\TestCase;

/**
 * Class SelectWriterTest.
 */
class SelectWriterTest extends TestCase
{
    private GenericBuilder $writer;
    private Select $query;
    private string $exceptionClass = QueryException::class;

    protected function setUp(): void
    {
        $this->writer = new GenericBuilder();
        $this->query = new Select();
        $this->query->setBuilder($this->writer); // Ensure the main query object has a builder
    }

    /**
     * @test
     */
    public function itShouldBeCloneableWithoutKeepingReferences(): void
    {
        $query1 = new Select('user');
        $query1->setBuilder($this->writer); // Important for __clone's composed objects if they need builder
        $query2 = clone $query1;
        $query2->setTable('users');

        // After cloning and changing table, the table objects should not be the same.
        $table1 = $query1->getTable();
        $table2 = $query2->getTable();

        $this->assertNotNull($table1);
        $this->assertNotNull($table2);
        $this->assertNotSame($table1->getName(), $table2->getName());
    }

    /**
     * @test
     */
    public function itShouldBeConstructedWithConstructor(): void
    {
        $this->query = new Select('user');
        $expected = 'SELECT user.* FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToWriteCommentInQuery(): void
    {
        $this->query = new Select('user');
        $this->query->setComment('This is a comment');
        $expected = <<<SQL
-- This is a comment
SELECT user.* FROM user
SQL;
        $this->assertSame(str_replace("\r\n", "\n", $expected), $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenGettingColumnsButNoTableIsSet(): void
    {
        $this->expectException($this->exceptionClass);
        $this->query = new Select(); // No table set
        $this->query->getColumns();
    }

    /**
     * @test
     */
    public function itShouldBeConstructedWithConstructorWithColumns(): void
    {
        $this->query = new Select('user', ['user_id', 'name']);
        $expected = 'SELECT user.user_id, user.name FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldSelectAll(): void
    {
        $this->query->setTable('user');
        $expected = 'SELECT user.* FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldSelectAllDistinct(): void
    {
        $this->query->setTable('user')->distinct();
        $expected = 'SELECT DISTINCT user.* FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldSelectAllWithLimit1(): void
    {
        $this->query->setTable('user')->limit(1, 0); // limit(count, offset) or limit(start,count)
        $expected = 'SELECT user.* FROM user LIMIT :v1, :v2';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 0];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldSelectAllWithLimit1Offset2(): void
    {
        $this->query->setTable('user')->limit(2, 1); // start=2 (offset), count=1
        $expected = 'SELECT user.* FROM user LIMIT :v1, :v2';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 2, ':v2' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldSelectAllGetFirst20(): void
    {
        $this->query->setTable('user')->limit(0, 20); // start=0 (offset), count=20
        $expected = 'SELECT user.* FROM user LIMIT :v1, :v2';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 0, ':v2' => 20];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldAllowColumnAlias(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email', // Changed
            ]);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email FROM user';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldAllowColumnOrder(): void
    {
        $this->query
            ->setTable('user')
            ->orderBy('user_id', OrderBy::ASC);
        $expected = 'SELECT user.* FROM user ORDER BY user.user_id ASC';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldAllowColumnOrderUsingColumnAlias(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email', // Changed
            ])
            ->orderBy('user_id', OrderBy::ASC)
            ->orderBy('email', OrderBy::DESC);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email FROM user ORDER BY user.user_id ASC, user.email DESC';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoALeftJoin(): void
    {
        $this->query
            ->setTable('user')
            ->leftJoin('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at']);
        $expected = 'SELECT user.*, news.title, news.body, news.created_at, news.updated_at FROM user LEFT JOIN news ON (news.author_id = user.user_id)';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoARightJoin(): void
    {
        $this->query
            ->setTable('user')
            ->rightJoin('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at']);
        $expected = 'SELECT user.*, news.title, news.body, news.created_at, news.updated_at FROM user RIGHT JOIN news ON (news.author_id = user.user_id)';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoAInnerJoin(): void
    {
        $this->query
            ->setTable('user')
            ->innerJoin('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at']);
        $expected = 'SELECT user.*, news.title, news.body, news.created_at, news.updated_at FROM user INNER JOIN news ON (news.author_id = user.user_id)';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoACrossJoin(): void
    {
        $this->query
            ->setTable('user')
            ->crossJoin('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at']);
        $expected = 'SELECT user.*, news.title, news.body, news.created_at, news.updated_at FROM user CROSS JOIN news ON (news.author_id = user.user_id)';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoALeftJoinWithOrderByOnJoinedTable(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->orderBy('user_id', OrderBy::DESC)
            ->leftJoin('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at'])
            ->orderBy('created_at', OrderBy::DESC);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email AS "email", user.created_at, news.title, news.body, news.created_at, news.updated_at FROM user LEFT JOIN news ON (news.author_id = user.user_id) ORDER BY user.user_id DESC, news.created_at DESC';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoAJoin(): void
    {
        $this->query
            ->setTable('user')
            ->join('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at']);
        $expected = 'SELECT user.*, news.title, news.body, news.created_at, news.updated_at FROM user JOIN news ON (news.author_id = user.user_id)';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoAJoinWithOrderByOnJoinedTable(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->orderBy('user_id', OrderBy::DESC)
            ->join('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at'])
            ->orderBy('created_at', OrderBy::DESC);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email AS "email", user.created_at, news.title, news.body, news.created_at, news.updated_at FROM user JOIN news ON (news.author_id = user.user_id) ORDER BY user.user_id DESC, news.created_at DESC';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoAJoinWithCustomColumns(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->orderBy('user_id', OrderBy::DESC)
            ->join('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at'])
            ->orderBy('created_at', OrderBy::DESC)
            ->join('articles', new Column('news_id', 'article'), new Column('id', 'news'));
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email AS "email", user.created_at, news.title, news.body, news.created_at, news.updated_at FROM user JOIN news ON (news.author_id = user.user_id) JOIN articles ON (news.id = article.news_id) ORDER BY user.user_id DESC, news.created_at DESC';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDoAnAddWithMultipleJoins(): void
    {
        $this->query->setTable('user');
        for ($i = 1; $i <= 5; ++$i) {
            $select = new Select();
            $select->setBuilder($this->writer); // Set builder
            $select
                ->setTable('news' . $i)
                ->setColumns(['title' . $i]);
            $this->query->addJoin($select, 'user_id', 'author_id' . $i);
        }
        $expected = 'SELECT user.*, news1.title1, news2.title2, news3.title3, news4.title4, news5.title5 FROM user JOIN news1 ON (news1.author_id1 = user.user_id) JOIN news2 ON (news2.author_id2 = user.user_id) JOIN news3 ON (news3.author_id3 = user.user_id) JOIN news4 ON (news4.author_id4 = user.user_id) JOIN news5 ON (news5.author_id5 = user.user_id)';
        $this->assertSame($expected, $this->writer->write($this->query));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToOn(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->orderBy('user_id', OrderBy::DESC)
            ->join('news', 'user_id', 'author_id', ['title', 'body', 'created_at', 'updated_at'])
            ->orderBy('created_at', OrderBy::DESC)
            ->on()
            ->eq('author_id', 1);
        $this->query->limit(1, 10);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email AS "email", user.created_at, news.title, news.body, news.created_at, news.updated_at FROM user JOIN news ON (news.author_id = user.user_id) AND (news.author_id = :v1) ORDER BY user.user_id DESC, news.created_at DESC LIMIT :v2, :v3';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 1, ':v3' => 10];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCountTotalRows(): void
    {
        $this->query
            ->setTable('user')
            ->count()
            ->groupBy(['user_id', 'name'])
            ->having()
            ->equals('user_id', 1)
            ->equals('user_id', 2);
        $expected = 'SELECT COUNT(*) FROM user GROUP BY user.user_id, user.name HAVING (user.user_id = :v1) AND (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCountTotalRowsSettingDefaultColumn(): void
    {
        $this->query
            ->setTable('user')
            ->count('user_id')
            ->groupBy(['user_id', 'name'])
            ->having()
            ->equals('user_id', 1)
            ->equals('user_id', 2);
        $expected = 'SELECT COUNT(user.user_id) FROM user GROUP BY user.user_id, user.name HAVING (user.user_id = :v1) AND (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCountTotalRowsSettingDefaultColumnWithAlias(): void
    {
        $this->query
            ->setTable('user')
            ->count('user_id', 'total_users')
            ->groupBy(['user_id', 'name'])
            ->having()
            ->equals('user_id', 1)
            ->equals('user_id', 2);
        $expected = 'SELECT COUNT(user.user_id) AS "total_users" FROM user GROUP BY user.user_id, user.name HAVING (user.user_id = :v1) AND (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToGroupByOperator(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->groupBy(['user_id', 'name'])
            ->having()
            ->equals('user_id', 1)
            ->equals('user_id', 2);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email AS "email", user.created_at FROM user GROUP BY user.user_id, user.name HAVING (user.user_id = :v1) AND (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionInvalidHavingConjunction(): void
    {
        $this->expectException($this->exceptionClass);
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->groupBy(['user_id', 'name'])
            ->having('AAAAAAAAAAAAAAAA');
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSetHavingOperatorToOr(): void
    {
        $this->query
            ->setTable('user')
            ->setColumns([
                'userId' => 'user_id',
                'username' => 'name',
                'email' => 'email',
                'created_at',
            ])
            ->groupBy(['user_id', 'name'])
            ->having('OR')
            ->equals('user_id', 1)
            ->equals('user_id', 2);
        $expected = 'SELECT user.user_id AS "userId", user.name AS "username", user.email AS "email", user.created_at FROM user GROUP BY user.user_id, user.name HAVING (user.user_id = :v1) OR (user.user_id = :v2)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldAllowSelectQueryToActAsAColumn(): void
    {
        $table1 = new Select('Table1');
        $table1->setBuilder($this->writer);
        $table1->where()->equals('table1_id', 1);

        $table2 = new Select('Table2');
        $table2->setBuilder($this->writer);
        $table2->where()->eq($table1, 2); // $table1 is a Select object

        $expected = 'SELECT Table2.* FROM Table2 WHERE ((SELECT Table1.* FROM Table1 WHERE (Table1.table1_id = :v1)) = :v2)';
        $this->assertSame($expected, $this->writer->write($table2));
        $expectedValues = [':v1' => 1, ':v2' => 2];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }

    /**
     * @test
     */
    public function itShouldWriteJoin(): void
    {
        $this->query->setBuilder($this->writer); // Ensure main query has builder
        $this->query
            ->isJoin(true) // This now returns self (Select instance)
            ->setTable('user')
            ->on()
            ->equals('user_id', 1);

        $expected = 'JOIN user ON (user.user_id = :v1)';
        $this->assertSame($expected, $this->writer->write($this->query));
        $expectedValues = [':v1' => 1];
        $this->assertEquals($expectedValues, $this->writer->getValues());
    }
}
