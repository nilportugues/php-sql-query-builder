SQL Query Builder
=================

[![Build Status](https://travis-ci.org/nilportugues/php-sql-query-builder.svg)](https://travis-ci.org/nilportugues/php-sql-query-builder) [![Coverage Status](https://img.shields.io/coveralls/nilportugues/sql-query-builder.svg)](https://coveralls.io/r/nilportugues/sql-query-builder) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/sql-query-builder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/sql-query-builder/?branch=master)  [![SensioLabsInsight](https://insight.sensiolabs.com/projects/89ec1003-4227-43a2-8432-67a9fc2d3ba3/mini.png)](https://insight.sensiolabs.com/projects/89ec1003-4227-43a2-8432-67a9fc2d3ba3) [![Latest Stable Version](https://poser.pugx.org/nilportugues/sql-query-builder/v/stable)](https://packagist.org/packages/nilportugues/sql-query-builder) [![Total Downloads](https://poser.pugx.org/nilportugues/sql-query-builder/downloads)](https://packagist.org/packages/nilportugues/sql-query-builder) [![License](https://poser.pugx.org/nilportugues/sql-query-builder/license)](https://packagist.org/packages/nilportugues/sql-query-builder)
[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://paypal.me/nilportugues)

An elegant lightweight and efficient SQL Query Builder with fluid interface SQL syntax supporting bindings and complicated query generation. **Works without establishing a connection to the database.** 

<a name="index_block"></a>

* [1. Installation](#block1)
* [2. The Builder](#block2)
    * [2.1. Generic Builder](#block2.1)     
    * [2.2. MySQL Builder](#block2.2)     
    * [2.3. Human Readable Output](#block2.3)     
* [3. Building Queries](#block3)
    * [3.1. SELECT Statement](#block3.1)     
        * [3.1.1. Basic SELECT statement](#block3.1.1) 
        * [3.1.2. Aliased SELECT statement](#block3.1.2)
        * [3.1.3. SELECT with WHERE statement](#block3.1.3)
        * [3.1.4. Complex WHERE conditions](#block3.1.4)
        * [3.1.5. JOIN & LEFT/RIGHT/INNER/CROSS JOIN SELECT statements](#block3.1.5)
        * [3.1.6. COUNT rows](#block3.1.6)
    * [3.2. INSERT Statement](#block3.2)
           * [3.2.1. Basic INSERT statement](#block3.2.1) 
    * [3.3. UPDATE Statement](#block3.3)
        * [3.3.1. Basic UPDATE statement](#block3.3.1)
        * [3.3.2. Elaborated UPDATE statement](#block3.3.2)
    * [3.4. DELETE Statement](#block3.4)     
        * [3.4.1. Empty table with DELETE statement](#block3.4.1)
        * [3.4.2. Basic DELETE statement](#block3.4.2)
        * [3.4.3. Elaborated DELETE statement](#block3.4.3)     
    * [3.5. INTERSECT Statement](#block3.5)
    * [3.6. MINUS Statement](#block3.6)
    * [3.7. UNION Statement](#block3.7)
    * [3.8. UNION ALL Statement](#block3.8)
* [4. Advanced Quering](#block4)    
    * [4.1. Filtering using WHERE](#block4.1)
        * [4.1.1. Changing WHERE logical operator](#block4.2)     
        * [4.1.2. Writing complicated WHERE conditions](#block4.2)
    * [4.3. Grouping with GROUP BY and HAVING](#block4.3)     
        * [4.3.1 Available HAVING operators](#block4.3.1)     
    * [4.4. Changing HAVING logical operator](#block4.4)     
    * [4.5. Columns as SELECT statements](#block4.5)        
    * [4.6. Columns being Values](#block4.6)
    * [4.7. Columns using FUNCTIONS](#block4.7)
* [5. Commenting queries](#block5)
* [6. Quality Code](#block6)
* [7. Author](#block7)
* [8. License](#block8)


<a name="block1"></a>
## 1. Installation [↑](#index_block)
The recommended way to install the SQL Query Builder is through [Composer](http://getcomposer.org). Run the following command to install it:

```sh
php composer.phar require nilportugues/sql-query-builder
```

<a name="block2"></a>
## 2. The Builder [↑](#index_block)

The SQL Query Builder allows to generate complex SQL queries standard using the `SQL-2003` dialect (default) and the `MySQL` dialect, that extends the `SQL-2003` dialect.

<a name="block2.1"></a>
### 2.1. Generic Builder [↑](#index_block)
The Generic Query Builder is the default builder for this class and writes standard SQL-2003.

**All column aliases are escaped using the `'` sign by default.**

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()->setTable('user');    

echo $builder->write($query);    
```
#### Output:
```sql
SELECT user.* FROM user
```

<a name="block2.2"></a>
### 2.2. MySQL Builder [↑](#index_block) 
The MySQL Query Builder has its own class, that inherits from the SQL-2003 builder. All columns will be wrapped with the tilde **`** sign.

**All table and column aliases are escaped using the tilde sign by default.**

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;

$builder = new MySqlBuilder(); 

$query = $builder->select()->setTable('user'); 

echo $builder->write($query);    
```
#### Output:
```sql
SELECT user.* FROM `user` 
```

<a name="block2.3"></a>
#### 2.3. Human Readable Output [↑](#index_block)

Both Generic and MySQL Query Builder can write complex SQL queries. 

Every developer out there needs at some point revising the output of a complicated query, the SQL Query Builder includes a human-friendly output method, and therefore the `writeFormatted` method is there to aid the developer when need. 

Keep in mind `writeFormatted` is to be avoided at all cost in production mode as it adds unneeded overhead due to parsing and re-formatting of the generated statement.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()->setTable('user');  

echo $builder->writeFormatted($query);    

```
#### Output:
```sql
SELECT 
    user.* 
FROM 
    user
```

More complicated examples can be found in the documentation.


<a name="block3"></a>
## 3. Building Queries [↑](#index_block)

<a name="block3.1"></a>
### 3.1. SELECT Statement [↑](#index_block) 



<a name="block3.1.1"></a>
#### 3.1.1. Basic SELECT statement [↑](#index_block)
#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns(['user_id','name','email']);
     
echo $builder->write($query);    
```
#### Output:
```sql
SELECT user.user_id, user.name, user.email FROM user
```

<a name="block3.1.2"></a>
#### 3.1.2. Aliased SELECT statement [↑](#index_block) 

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns(['userId' => 'user_id', 'username' => 'name', 'email' => 'email']);
       
echo $builder->write($query);    
```
#### Output:
```sql
SELECT user.user_id AS 'userId', user.name AS 'username', user.email AS 'email' FROM user
```
<a name="block3.1.3"></a>
#### 3.1.3. SELECT with WHERE statement [↑](#index_block)

Default logical operator for filtering using `WHERE` conditions is `AND`.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns([
        'userId' => 'user_id',
        'username' => 'name',
        'email' => 'email'
    ])
    ->where()
    ->greaterThan('user_id', 5)
    ->notLike('username', 'John');
      
echo $builder->writeFormatted($query);    
```
#### Output:
```sql
SELECT 
    user.user_id AS 'userId',
    user.name AS 'username',
    user.email AS 'email'
FROM 
    user 
WHERE 
    (user.user_id < :v1)
    AND (user.username NOT LIKE :v2)
```

<a name="block3.1.4"></a>
#### 3.1.4. Complex WHERE conditions [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->where()
    ->equals('user_id', 1)
    ->equals('user_id', 2)
    ->subWhere("OR")
    ->lessThan($column, 10)
    ->greaterThan('user_id', 100);

echo $builder->writeFormatted($query);
```

#### Output:
```sql
SELECT
    user.*
FROM
    user
WHERE
    (user.user_id = :v1)
    AND (user.user_id = :v2)
    AND (
        (user.user_id < :v3)
        OR (user.user_id > :v4)
    )
```

<a name="block3.1.5"></a>
#### 3.1.5. JOIN & LEFT/RIGHT/INNER/CROSS JOIN SELECT statements [↑](#index_block)

Syntax for `JOIN`, `LEFT JOIN`, `RIGHT JOIN`, `INNER JOIN`, `CROSS JOIN` work the exactly same way. 

Here's an example selecting both table and joined table columns and doing sorting using columns from both the table and the joined table.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns([
            'userId'   => 'user_id',
            'username' => 'name',
            'email'    => 'email',
            'created_at'
    ])
    ->orderBy('user_id', OrderBy::DESC)
    ->leftJoin(
        'news', //join table
        'user_id', //origin table field used to join
        'author_id', //join column
         ['newsTitle' => 'title', 'body', 'created_at', 'updated_at']
     )
    ->on()
    ->equals('author_id', 1); //enforcing a condition on the join column

$query
    ->where()
    ->greaterThan('user_id', 5)
    ->notLike('username', 'John');

$query
    ->orderBy('created_at', OrderBy::DESC);

echo $builder->writeFormatted($query); 
```
#### Output:
```sql
SELECT 
    user.user_id AS 'userId',
    user.name AS 'username',
    user.email AS 'email',
    user.created_at,
    news.title AS 'newsTitle',
    news.body,
    news.created_at,
    news.updated_at 
FROM 
    user 
LEFT JOIN 
        news
    ON 
        (news.author_id = user.user_id) 
        AND (news.author_id = :v1)
WHERE 
    (user.user_id < :v2)
    AND (user.username NOT LIKE :v3)        
ORDER BY 
    user.user_id DESC,
    news.created_at DESC;
```

<a name="block3.1.6"></a>
#### 3.1.6. COUNT rows [↑](#index_block)
Counting rows comes in 3 possible ways, using the ALL selector `*`, stating a column or stating a column and its alias.

#### 3.1.6.1. Count using ALL selector
#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
        ->setTable('user')
        ->count()

echo $builder->write($query);
```

#### Output:
```sql
SELECT COUNT(*) FROM user;
```

#### 3.1.6.2. Count using column as a selector
#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
        ->setTable('user')
        ->count('user_id')

echo $builder->write($query);
```

#### Output:
```sql
SELECT COUNT(user.user_id) FROM user;
```

#### 3.1.6.3. Count using column as a selector
#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
        ->setTable('user')
        ->count('user_id', 'total_users')

echo $builder->write($query);
```

#### Output:
```sql
SELECT COUNT(user.user_id) AS 'total_users' FROM user;
```

<a name="block3.2"></a>
### 3.2. INSERT Statement [↑](#index_block)

The `INSERT` statement is really straightforward.

<a name="block3.2.1"></a>
#### 3.2.1 Basic INSERT statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->insert()
    ->setTable('user')
    ->setValues([
        'user_id' => 1,
        'name'    => 'Nil',
        'contact' => 'contact@nilportugues.com',
    ]);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output
```sql
INSERT INTO user (user.user_id, user.name, user.contact) VALUES (:v1, :v2, :v3)
```

```php
[':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com'];
```

<a name="block3.3"></a>
### 3.3. UPDATE Statement [↑](#index_block)

The `UPDATE` statement works just like expected, set the values and the conditions to match the row and you're set. 

Examples provided below.

<a name="block3.3.1"></a>
#### 3.3.1 Basic UPDATE statement [↑](#index_block)
Important including the the `where` statement is critical, or all table rows will be replaced with the provided values if the statement is executed.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->update()
    ->setTable('user')
    ->setValues([
        'user_id' => 1,
        'name' => 'Nil',
        'contact' => 'contact@nilportugues.com'
    ])
    ->where()
    ->equals('user_id', 1);

$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
UPDATE 
    user 
SET
    user.user_id = :v1,
    user.name = :v2, 
    user.contact = :v3
WHERE 
    (user.user_id = :v4)
```
```php
[':v1' => 1, ':v2' => 'Nil', ':v3' => 'contact@nilportugues.com', ':v4' => 1];
```
<a name="block3.3.2"></a>
#### 3.3.2. Elaborated UPDATE statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->update()
    ->setTable('user')
    ->setValues([
        'name' => 'UpdatedName',
    ]);
    
$query
    ->where()
    ->like('username', '%N')
    ->between('user_id', 1, 2000);
        
$query
    ->orderBy('user_id', OrderBy::ASC)
    ->limit(1);            
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
UPDATE 
    user 
SET 
    user.name = :v1
WHERE 
    (user.username LIKE :v2) 
    AND (user.user_id BETWEEN :v3 AND :v4)
ORDER BY 
    user.user_id ASC 
LIMIT :v5
```

<a name="block3.4"></a>
### 3.4. DELETE Statement [↑](#index_block)

The `DELETE` statement is used just like `UPDATE`, but no values are set. 

Examples provided below.

<a name="block3.4.1"></a>
#### 3.4.1. Empty table with DELETE statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->delete()
    ->setTable('user');
   
$sql = $builder->write($query);   
```
#### Output:
```sql
DELETE FROM user
```

<a name="block3.4.2"></a>
#### 3.4.2. Basic DELETE statement [↑](#index_block)
Important including the the `where` statement is critical, or all table rows will be deleted with the provided values if the statement is executed.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->delete()
    ->setTable('user');

$query
    ->where()
    ->equals('user_id', 100);

$query
    ->limit(1);
   
$sql = $builder->write($query);    
$values = $builder->getValues();
```
#### Output:
```sql
DELETE FROM user WHERE (user.user_id = :v1) LIMIT :v2
```
```php
[':v1' => 100, ':v2' => 1];
```
<a name="block3.4.2"></a>
#### 3.4.2. Elaborated DELETE statement [↑](#index_block) 

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->delete()
    ->setTable('user');
    
$query
    ->where()
    ->like('username', '%N')
    ->between('user_id', 1, 2000);
        
$query
    ->orderBy('user_id', OrderBy::ASC)
    ->limit(1);            
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
DELETE FROM 
    user 
WHERE 
    (user.username LIKE :v1) 
    AND (user.user_id BETWEEN :v2 AND :v3)
ORDER BY 
    user.user_id ASC 
LIMIT :v4
```



<a name="block3.5"></a>
### 3.5. INTERSECT Statement [↑](#index_block)

***
   INTERSECT is not supported by MySQL. 
   Same results can be achieved by using INNER JOIN statement instead.
***

The `INTERSECT` statement is really straightforward.

<a name="block3.5.1"></a>
#### 3.5.1 Basic INTERSECT statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$select1 = $builder->select()->setTable('user');
$select2 = $builder->select()->setTable('user_emails');
   
$query = $builder->intersect()
    ->add($select1)
    ->add($select2);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output
```sql
SELECT user.* FROM user
INTERSECT
SELECT user_email.* FROM user_email
```


<a name="block3.6"></a>
### 3.6. MINUS Statement [↑](#index_block)

***
   MINUS is not supported by MySQL. 
   Same results can be achieved by using a LEFT JOIN statement 
   in combination with an IS NULL or NOT IN condition instead.
***

The `MINUS` statement is really straightforward.

<a name="block3.6.1"></a>
#### 3.6.1 Basic MINUS statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$select1 = $builder->select()->setTable('user');
$select2 = $builder->select()->setTable('user_emails');
   
$query = $builder->minus($select1, $select2);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output
```sql
SELECT user.* FROM user
MINUS
SELECT user_email.* FROM user_email
```


<a name="block3.7"></a>
### 3.7. UNION Statement [↑](#index_block)

The `UNION` statement is really straightforward.

<a name="block3.7.1"></a>
#### 3.7.1 Basic UNION statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$select1 = $builder->select()->setTable('user');
$select2 = $builder->select()->setTable('user_emails');
   
$query = $builder->union()
    ->add($select1)
    ->add($select2);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output
```sql
SELECT user.* FROM user
UNION
SELECT user_email.* FROM user_email
```

<a name="block3.8"></a>
### 3.8. UNION ALL Statement [↑](#index_block)

The `UNION ALL` statement is really straightforward.

<a name="block3.8.1"></a>
#### 3.8.1 Basic UNION ALL statement [↑](#index_block)

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$select1 = $builder->select()->setTable('user');
$select2 = $builder->select()->setTable('user_emails');
   
$query = $builder->unionAll()
    ->add($select1)
    ->add($select2);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output
```sql
SELECT user.* FROM user
UNION ALL
SELECT user_email.* FROM user_email
```



<a name="block4"></a>
## 4. Advanced Quering [↑](#index_block)

<a name="block4.1"></a>
### 4.1. Filtering using WHERE [↑](#index_block)
The following operators are available for filtering using WHERE conditionals:

```php
public function subWhere($operator = 'OR');
public function equals($column, $value);        
public function notEquals($column, $value);
public function greaterThan($column, $value);
public function greaterThanOrEqual($column, $value);
public function lessThan($column, $value);
public function lessThanOrEqual($column, $value);
public function like($column, $value);
public function notLike($column, $value);
public function match(array $columns, array $values);
public function matchBoolean(array $columns, array $values);
public function matchWithQueryExpansion(array $columns, array $values);
public function in($column, array $values);
public function notIn($column, array $values);
public function between($column, $a, $b);
public function notBetween($column, $a, $b);
public function isNull($column);
public function isNotNull($column);
public function exists(Select $select);
public function notExists(Select $select);
public function addBitClause($column, $value);    
public function asLiteral($literal);
```

<a name="block4.2"></a>
### 4.2. Changing WHERE logical operator [↑](#index_block)

`WHERE` default's operator must be changed passing to the `where` method the logical operator `OR`.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->where('OR')
    ->equals('user_id', 1)
    ->like('name', '%N%');       
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
SELECT user.* FROM user WHERE (user.user_id = :v1) OR (user.name LIKE :v2)
```
        
<a name="block4.3"></a>
### 4.3. Grouping with GROUP BY and HAVING [↑](#index_block)

Default logical operator for joining more than one `HAVING` condition is `AND`.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns([
        'userId'   => 'user_id',
        'username' => 'name',
        'email'    => 'email',
        'created_at'
    ])
    ->groupBy(['user_id', 'name'])
    ->having()
    ->equals('user_id', 1)
    ->equals('user_id', 2);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
SELECT 
    user.user_id AS 'userId',
    user.name AS 'username',
    user.email AS 'email',
    user.created_at 
FROM 
    user 
GROUP BY 
    user.user_id, user.name 
HAVING 
    (user.user_id = :v1)
    AND (user.user_id = :v2)
```

<a name="block4.3.1"></a>
#### 4.3.1 Available HAVING operators  [↑](#index_block)
Same operators used in the WHERE statement are available for HAVING operations.

<a name="block4.4"></a>
### 4.4. Changing HAVING logical operator [↑](#index_block)

`HAVING` default's operator must be changed passing to the `having` method the logical operator `OR`.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns([
        'userId'   => 'user_id',
        'username' => 'name',
        'email'    => 'email',
        'created_at'
    ])
    ->groupBy(['user_id', 'name'])
    ->having('OR')
    ->equals('user_id', 1)
    ->equals('user_id', 2);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
SELECT 
    user.user_id AS 'userId',
    user.name AS 'username',
    user.email AS 'email',
    user.created_at 
FROM 
    user 
GROUP BY 
    user.user_id, user.name 
HAVING 
    (user.user_id = :v1)
    OR (user.user_id = :v2)
```

<a name="block4.5"></a>
### 4.5. Columns as SELECT statements [↑](#index_block)

Sometimes, a column needs to be set as a column. SQL Query Builder got you covered on this one too! Check the example below.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$selectRole = $builder->select()
    ->setTable('role')
    ->setColumns(array('role_name'))
    ->limit(1)
    ->where()
    ->equals('role_id', 3);

$query = $builder->select()
    ->setTable('user')
    ->setColumns(array('user_id', 'username'))
    ->setSelectAsColumn(array('user_role' => $selectRole))
    ->setSelectAsColumn(array($selectRole))
    ->where()
    ->equals('user_id', 4);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
SELECT 
    user.user_id,
    user.username,
    (
        SELECT 
            role.role_name 
        FROM 
            role 
        WHERE 
            (role.role_id = :v1) 
        LIMIT :v2, :v3
    ) AS 'user_role', 
    (
        SELECT 
            role.role_name  
        FROM 
            role 
        WHERE 
            (role.role_id = :v4) 
        LIMIT :v5, :v6
    ) AS 'role' 
FROM 
    user 
WHERE 
    (user.user_id = :v7)
```

<a name="block4.6"></a>
### 4.6. Columns being Values [↑](#index_block)

There are time where you need to force the same column structure (eg: UNIONs) even when lacking of a column or value. Forcing column with values gets you covered.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns(array('user_id', 'username'))
    ->setValueAsColumn('10', 'priority')
    ->where()
    ->equals('user_id', 1);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```
#### Output:
```sql
SELECT 
    user.user_id,
    user.username,
    :v1 AS 'priority' 
FROM 
    user 
WHERE
    (user.user_id = :v2)
```

<a name="block4.7"></a>
### 4.7. Columns using FUNCTIONS [↑](#index_block)

Example for MAX function.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns(array('user_id', 'username'))
    ->setFunctionAsColumn('MAX', array('user_id'), 'max_id')
    ->where()
    ->equals('user_id', 1); 
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output:
```sql
SELECT 
    user.user_id,
    user.username,
    MAX(user_id) AS 'max_id'
FROM 
    user
WHERE
    (user.user_id = :v1)
```

Example for CURRENT_TIMESTAMP function.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setColumns(array('user_id', 'username'))
    ->setFunctionAsColumn('CURRENT_TIMESTAMP', array(), 'server_time')
    ->where()
    ->equals('user_id', 1);
   
$sql = $builder->writeFormatted($query);    
$values = $builder->getValues();
```

#### Output:
```sql
SELECT 
    user.user_id,
    user.username,
    CURRENT_TIMESTAMP AS 'server_time' 
FROM 
    user 
WHERE
    (user.user_id = :v1)
```

<a name="block5"></a>
## 5. Commenting queries [↑](#index_block)
The query builder allows adding comments to all query methods by using the `setComment` method.

Some useful use cases examples can be : 

 - Explain difficult queries or why of its existence. 
 - Finding slow queries from its comments.

#### Usage:
```php
<?php
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

$builder = new GenericBuilder(); 

$query = $builder->select()
    ->setTable('user')
    ->setComment('This is a comment');
    
$sql = $builder->write($query);   
```

#### Output:
```sql
-- This is a comment
SELECT user.* FROM user
```

<a name="block6"></a>
## 6. Quality Code [↑](#index_block)
Testing has been done using PHPUnit and [Travis-CI](https://travis-ci.org). All code has been tested to be compatible from PHP 5.4 up to PHP 5.6 and [HHVM](http://hhvm.com/).

To run the test suite, you need [Composer](http://getcomposer.org):

```bash
    php composer.phar install --dev
    php bin/phpunit
```


<a name="block7"></a>
## 7. Author [↑](#index_block)
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)


<a name="block8"></a>
## 8. License [↑](#index_block)
SQL Query Builder is licensed under the MIT license.

```
Copyright (c) 2014 Nil Portugués Calderó

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
