# BMware DMK

BMware database management kit

basic setup example

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
])->define(function($context){
  $context("modelDatabaseWithSchema", "schema.xml")
});
```

`DatabaseConfig::create()` takes in an array of arguments.

- servername: the base url to you local/test/production/ site
- username: the username of the database
- password: the password of the database
- useExistingDatabase: boolean, choose wether to use a pre-existing database, or make a new one
- databaseName: the name of the database that you want to use, if useExistingDatabase is false
  this becomes the name of the new database. Leave empty to use the default database name: bmbuilder_testing

the define function is called to set up the initial schema for the database, this is done to unlock migrations. Right from the start, however this is optional

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

_note that when using the Query object you need to return it, but not when using a custom query(yet)_

---

### Migrations

similar to the Query object, migrations also have their own object. a simple migration example looks the following.

```php
use access\Migration;

$database->define(function($context){
  return Migration::start(["start"], $context("databaseSchema"))
  ->create([
    "ID" => "INT(255) NOT NULL AUTO_INCREMENT",
    "email" => "VARCHAR(255) NOT NULL",
    "created at" => "DATETIME DEFAULT CURRENT_TIMESTAMP",
    "PRIMARY" => "ID"
  ])
  ->endQuery();
});
```

_note that a custom migration class will be added later, to abstract the calls even more. but it will translate to these calls which in turn will translate back into MySQLi statements_

`Migration::start()` takes in 2 arguments:

- the name of the table(s) that you want to effect.
- the schema of the database, which is called with the `$context("databaseSchema")` function that is passed as a parameter

there are currently 3 query statements predefined in this library:

- create

```php
use access\Migration;

$database->define(function($context){
  return Migration::start(["start"], $context("databaseSchema"))
  ->create([
    "ID" => "INT(255) NOT NULL AUTO_INCREMENT",
    "email" => "VARCHAR(255) NOT NULL",
    "created_at" => "DATETIME DEFAULT CURRENT_TIMESTAMP",
    "PRIMARY" => "ID"
  ])
  ->endQuery();
});
```

- update

_note you can update the table name, and the fields_

```php
use access\Migration;

$database->define(function($context){
  //currently only 1 query can be made per define, this wil change in the future, so remove the statements you dont want to try

  //alter table name
  return Migration::start(["start"], $context("databaseSchema"))
  ->alter(["values" => "users"])
  ->endQuery();

  //alter field name and structure
  return Migration::start(["users"], $context("databaseSchema"))
  ->alter([
    "selectors" => "email",
    "values" => ["e_mail" => "VARCHAR(20)"]
  ])
  ->endQuery();

  //alter field structure
  return Migration::start(["users"], $context("databaseSchema"))
  ->alter([
    "selectors" => "e_mail",
    "values" => "VARCHAR(30) DEFAULT 'boydvree@BMware.com'"
  ])
  ->endQuery();
});
```

- delete

_note you can delete tables or fields_

```php
use \access\Migration;

$database->define(function($context){
  //drop field
  return Migration::start(["users"], $context("databaseSchema"))
  ->drop(["e_mail", "created_at"])
  ->endQuery();

  //drop table, multiple tables can be dropped at once by adding more to the array of tables
  return Migration::start(["users"], $context("databaseSchema"))
  ->drop()
  ->endQuery();
});
```

---

# Something to talk about

In my eyes, what makes this library "good" is that the query definitions are done in functions. meaning that you can let your programming spirit loose on it, and set up all kind of conditional checks on for example the schema. before the query is even excecuted this means that you can do just about everything before the query is excecuted.

Also, something that i want to work on in the near future, Which probably wont be worked on in a while because that part of the lib wont be usefull for my work, is adding support for json structured queries. seeing as the calls are stored in a query object before even passing down to the decoder anyway. this will allow for sharing and/or making query templates. which in my eyes seems like an awesome feature.

# Planning

this is a list of things that I want to add

1. Wordpress config and database class, custom config class that skipps the unnecesary setup steps and a custom database class that will redefine how the queries are made.
2. DatabaseResult class, custom result class with modifiers that let's you easily access certain sets of the data, and call functions on collections.
3. Cacheing, flagged queries can be cached in (potentially) session to make repetative calls quicker
4. Multiple queries in define, allow the user a way to create multiple queries in one define statement (if it calls for it, will make JSON queries a priority).

### the above points will mark the release of the library, after this I will mostly stop developing the library for work, and work on it only as a personal project

---

4. SchemaEngine, this wil cointain functionality like: create from existing database, update on migration and create alongside database
5. Migration abstratction class, a class that will make migration calss easier. and excecuted without using the define function.
6. Query abstraction class, similar to the migration abstraction class, but then from within the application and not from a script
7. CLI tool, a cli tool to make migrations easier.
8. Custom abstraction classes. allow users to easily make abstratction classes and hook them into the library
9. Support for JSON query objects and abstract JSON query objects.

any and all suggestions are welcome.
