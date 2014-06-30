SQL Query Builder
=================

[![Build Status](https://travis-ci.org/nilportugues/sql-query-builder.png)](https://travis-ci.org/nilportugues/sql-query-builder) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/89ec1003-4227-43a2-8432-67a9fc2d3ba3/mini.png)](https://insight.sensiolabs.com/projects/89ec1003-4227-43a2-8432-67a9fc2d3ba3) [![Latest Stable Version](https://poser.pugx.org/nilportugues/sql-query-builder/v/stable.svg)](https://packagist.org/packages/nilportugues/sql-query-builder) [![Total Downloads](https://poser.pugx.org/nilportugues/sql-query-builder/downloads.svg)](https://packagist.org/packages/nilportugues/sql-query-builder) [![License](https://poser.pugx.org/nilportugues/sql-query-builder/license.svg)](https://packagist.org/packages/nilportugues/sql-query-builder)

Builds sitemaps for pages, images and media files and provides a class to submit them to search engines.

* [1. Installation](#block1)
* [2. The Builder](#block2)
	* [2.1. SQL Builder](#block2.1)
	* [2.2. MySQL Builder](#block2.2)
* [3. Building Queries](#block3)
	* [3.1. SELECT Statement](#block3.1)
	* [3.2. INSERT Statement](#block3.2)
	* [3.3. UPDATE Statement](#block3.3)
	* [3.4. DELETE Statement](#block3.4)
* [4. Advanced Queries](#block4)
	* [4.1. Filtering using WHERE](#block4.1)
		* [4.1.1 Available operators](#block4.1)
	* [4.2. Changing WHERE logical operator](#block4.2)
	* [4.3. Grouping with GROUP BY and HAVING](#block4.3)
		* [4.3.1 Available HAVING operators](#block4.3.1)
	* [4.4. Changing HAVING logical operator](#block4.4)
	* [4.5. Columns as SELECT statements](#block4.5)
	* [4.6. Columns using FUNCTIONS](#block4.6)
* [5. Quality Code](#block5)
* [6. Author](#block6)
* [7. License](#block7)

---
<a name="block1"></a>
## 1. Installation
The recommended way to install SQL Query Builder is through [Composer](http://getcomposer.org). Just create a ``composer.json`` file and run the ``php composer.phar install`` command to install it:

```json
  {
      "require": {
          "nilportugues/sql-query-builder": "1.0.0"
      }
  }
```

----

<a name="block5"></a>
## 5. Quality Code
Testing has been done using PHPUnit and [Travis-CI](https://travis-ci.org). All code has been tested to be compatible from PHP 5.4 up to PHP 5.6 and [HHVM (nightly release)](http://hhvm.com/).

To run the test suite, you need [Composer](http://getcomposer.org):

```bash
    php composer.phar install --dev
    bin/phpunit
```
---

<a name="block6"></a>
## 6. Author
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)

---

<a name="block7"></a>
## 7. License
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
