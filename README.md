# BMware DMK

BMware database management kit

there are currently 2 basic setup examples, the first is the normal db setup:

```PHP
require_once("vendor/autoload.php");

use config\DatabaseConfig;
use access\Query;

$database = DatabaseConfig::create([
  "servername" => "localhost",
  "username" => "root",
  "password" => "",
  "useExistingDatabase" => true,
  "databaseName" => "bmbuilder_testing"
]);
```

`DatabaseConfig::create()` takes in an array of arguments.

- servername: the base url to you local/test/production/ site
- username: the username of the database
- password: the password of the database
- useExistingDatabase: boolean, choose wether to use a pre-existing database, or make a new one
- databaseName: the name of the database that you want to use, if useExistingDatabase is false
  this becomes the name of the new database. Leave empty to use the default database name: bmbuilder_testing

The second one is a WordPress based setup.

```PHP
require_once("vendor/autoload.php");

use config\WordpressDatabaseConfig;
use access\Query;

$database = WordpressDatabaseConfig::create();
```

_other than then the setup, using either class is the same. however, under the hood, the WordpressDatabase uses the global wpdb to make queries to the database_

---

### Queries

there are currently 4 query actions prebuild with this library:

- create

```PHP
use access\Query;

$database->define(function($context){
  return Query::start("test")
  ->insert(["selectors" => ["email"], "values" => ["boydvree@BMware.com"]])
  ->endQuery();
});
```

- read

```PHP
use access\Query;

$database->define(function($context){
  return Query::start("test")
  ->select(["selectors" => ["*"]])
  ->where(["email" => "boydvree@BMware.com"])
  ->endQuery();
});
```

- update

```PHP
use access\Query;

$database->define(function($context){
  return Query::start("test")
  ->update(["selectors" => ["name"], "values" => ["boyd"]])
  ->where(["email" => "boydvree@BMware.com"])
  ->endQuery();
});
```

- delete

```PHP
use access\Query;

$database->define(function($context){
  return Query::start("test")
  ->remove()
  ->where(["email" => "somerandomloser@notBMware.com"])
  ->endQuery();
});
```

_note that delete is a reserved word in php so I used remove instead_

the actions you define require an array to work, and depending on the action
will look for either of these 2 indexes:

- selectors: this defines the columns you want to target
- values: this defines the value of the column provided

`Query::start()` takes 1 argument:

- tableName: the name(s) of the tables in the transaction

`where()` takes in an array of key value pairs, where the key
is the column name to check. and the value is the value to validate for that field.

aditionally, a custom query can be made in the following way

```php
$database->define(function($context){
  $context("excecuteQuery", "SELECT * FROM `users`")
});
```

_note that when using the Query object you need to return it, but not when using a custom query_

---

### DatabaseResult

Every Query result will return a DatabaseResult object, which holds either a success message, or the rows of the query. You will also have access to various different result modifiers from that point, in order to select one out of the many results. This will allow you to query all of the data once, and internally select what you want to see at any given point. the data is never overridden so you will always have the result of query

example of the result modifiers in work

```PHP
$result->setUseModified(true) //tell the object to use the modifiedRow as the base for the next call

$result->getRowsByFieldValue("email", "hello@world.code")->selectFields("id")->getRows("modified"); // you can chain as many as you want toghetter however some might clash with eachother

$result->iterate(function($index, $row) use($someGlobalVariable){
  //this function here will be excecuted on all array items,
  //if the second variable is set, this function will be called on all values
  //the use() is used to bind $someGlobalVariable to the function scope this is completely optional
})->getRows("modified");
```

`$result->setUseModified(boolean $value)` sets the `$result->useModified` variable to the value of `$value`, which will make all modifier functions use the `$result->modifiedRows` as the base point for the function

`$result->getUseModified()` returns the current value of `$result->useModified`

`$result->getRowByIndex(int $index)` returns the row with the given `$index`

`$result->getRowsByFieldValue(string $field, string $value)` returns all rows where the given `$field` has the same value as `$value`

`$result->selectfields(string ...$fields)` returns only the fields specified in this function

`$result->getRows(string $flag)` depending on the value of `$flag` returns either the previous modified rows, base rows or current modified rows, the flags are: `"previous"` for the previous modified rows or `"modified"` for the current modified rows, anything else will just return the queries base result

`$result->iterate(callable $function, bool $recursive)` calls the `$function` on every row, or value depending on the value of `$recursive`. use `use($someGlobalVariable)` to bind a global variable to the function scope, use `use(&$someGlobalVariable)` to bind a global variable to the function scope and actually alter the variable

---

# Something to talk about

In my eyes, what makes this library "good" is that the query definitions are done in functions. meaning that you can let your programming spirit loose on it, and set up all kind of conditional checks on for example the schema. before the query is even excecuted this means that you can do just about everything before the query is excecuted.

Also, something that i want to work on in the near future, Which probably wont be worked on in a while because that part of the lib wont be usefull for my work, is adding support for json structured queries. seeing as the calls are stored in a query object before even passing down to the decoder anyway. this will allow for sharing and/or making query templates. which in my eyes seems like an awesome feature.

# Planning

1. Join statements, support for join statements in the query object
2. SchemaEngine, this wil cointain functionality like: create from existing database, update on migration and create alongside database
3. Migration abstratction class, a class that will make migration calss easier. and excecuted without using the define function.
4. Query abstraction class, similar to the migration abstraction class, but then from within the application and not from a script
5. CLI tool, a cli tool to make migrations easier.
6. Custom abstraction classes. allow users to easily make abstratction classes and hook them into the library
7. Support for JSON query objects and abstract JSON query objects.
8. Cacheing, flagged queries can be cached in (potentially) session to make repetative calls quicker

any and all suggestions are welcome.
