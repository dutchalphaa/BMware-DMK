# BmBuilder
database management toolkit

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
]);

$database->defineQuery(function($conn){
  Query::start($conn, "test")
  ->remove()
  ->where(["ID" => 1, "value" => "jemoeder"])
  ->endQuery();
});
```

`DatabaseConfig::create()` takes in and array of arguments.
- servername: the base url to you local/test/production/ site
- username: the username of the database
- password: the password of the database
- useExistingDatabase: boolean, choose wether to use a pre-existing database, or make a new one
- databaseName: the name of the database that you want to use, if useExistingDatabase is false
this becomes the name of the new database. Leave empty to use the default database name: bmbuilder_testing

---

there are currently 4 actions you can do with this library:
- create

```PHP
$database->defineQuery(function($conn){
  Query::start($conn, "test")
  ->insert(["selectors" => ["value"], "values" => ["hello world from the other side"]])
  ->endQuery();
});
```

- read

```PHP
$database->defineQuery(function($conn){
  Query::start($conn, "test")
  ->select(["selectors" => ["*"]])
  ->where(["ID" => 2])
  ->endQuery();
});
```

- update

```PHP
$database->defineQuery(function($conn){
  Query::start($conn, "test")
  ->update(["selectors" => ["value"], "values" => ["hallo wereld"]])
  ->where(["ID" => 2])
  ->endQuery();
});
```

- delete

```PHP
$database->defineQuery(function($conn){
  Query::start($conn, "test")
  ->remove()
  ->where(["ID" => 2])
  ->endQuery();
});
```
_note that delete is a reserved word in php so I used remove instead_

the actions you define require an array to work, and depending on the action
will look for either of these 2 properties:
- selectors: this defines the columns you want to target
- values: this defines the value of the column provided
