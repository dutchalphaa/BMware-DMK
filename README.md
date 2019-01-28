# BMware DMK

BMware database management kit

composer: `composer require bmware/dmk`

there are currently 2 basic setup examples, the first is the normal db setup:

```PHP
require_once("vendor/autoload.php");

use config\DatabaseConfig;

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

$database = WordpressDatabaseConfig::create();
```

_other than then the setup, using either class is the same. however, under the hood, the WordpressDatabase uses the global wpdb to make queries to the database_

---

### Queries

there are currently 4 query actions prebuild with this library:

- create

```PHP
use queries\CreateQuery;

$database->define(function($context){
  return CreateQuery::create("test")
  ->select("email", "location", "name")
  ->values("boydvree@BMware.com", "Netherlands", "boyd")
  ->values("someNoob@BMware.com", "noobland", "noob")
  ->endQuery();
});
```

- read

```PHP
use queries\ReadQuery;

$database->define(function($context){
  return ReadQuery::create("test")
  ->select()
  ->whereEquals("email", "boydvree@BMware.com")
  ->union()
  ->select()
  ->whereLessThan("email", "boydvre@BMware.com", true)
  ->endQuery();
});
```

- update

```PHP
use queries\UpdateQuery;

$database->define(function($context){
  return UpdateQuery::create("test")
  ->select("email")
  ->values("boydvree@BMware.com")
  ->whereEquals("email", "boydvree@BMware.com", true)
  ->endQuery();
});
```

- delete

```PHP
use queries\DeleteQuery;

$database->define(function($context){
  return DeleteQuery::create("test")
  ->whereGreateThan("ID", "10")
  ->endQuery();
});
```

each action has its own query object with its own modifiers.
all insert and where statements will automatically be turned into prepared statements.

every object has the `create(string $table)` function wich sets the table and returns a instance of the given query object.

every object has the `union(string $table = "")` function which if used creates a new instance of the given query object, and returns it. then on the end of the query, the bot of them will be put toghetter with a union. Will use the previous queries' table name if none is given

`CreateQuery`, `ReadQuery` and `DeleteQuery` have 3 "where" functions which are:

- `whereEquals(string $field, string $value, bool $notEquals = false)`: will add `"WHERE $field (!)= $value"` to the query
- `whereGreaterThan(string $field, string $value, bool $orEqualTo = false)`: will add `"WHERE $field >(=) $value"` to the query
- `whereLessThan(string $field, string $value, bool $orEqualTo = false)`: will add `"WHERE $field <(=) $value"` to the query

`ReadQuery` has a join function which takes in 3 arguments, to produce a join SQL statement

- `string $table` the table to join to the right of the first table
- `string $conditionOne` the first field to be used to join the 2 fields toghetter
- `string $condtionTwo` the second field to be used to join the 2 fields toghetter

the define function also accepts just the query as a argument

```php
$database->define(
  ReadQuery::create("test")
  ->select()
  ->whereEquals("email", "boydvree@BMware.com")
  ->union()
  ->select()
  ->whereLessThan("email", "boydvre@BMware.com", true)
  ->endQuery()
);
```

aditionally, a custom query can be made in the following way

```php
$database->define(function($context){
  return $context("excecuteQuery", "SELECT * FROM `users`")
});
```

_note that when using the function method, the query needs to be returned_

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

# Planning

1. SQL injection prevention, Automatically escape all the variables on the Query objects, to prevent any form of SQL injections
2. Migration support, make migration a thing that is easy to do
3. Schema support, make a standard schema definition that can be automatically transformed into a Create migration
4. CLI tool, a cli tool to make migrations easier.

any and all suggestions are welcome.
