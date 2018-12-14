<?php
//split into different traits and classes

//variableHelper - sets all the variables, will be called in the constructor function
//selectionHelper - will hold all of the selector functions.
//functionalHelper - holds all helper functions, like the database foreach(WIP) and reset function
//exceptionHelper - holds all of the exception logic that will be passed to endQuery
//DatabaseAccess - current class, will hold all of the base queries.
//DatabaseResult - new class, that will hold all of the data for the results
//DatabaseSetup - current setup class, holds support for initial setup of the database
//DatabaseSchema - new class, holds the schema of the database
//DatabaseMigration - new class, handles logic for database migrations
//IDatabaseCustomAcess - interface for defining custom database access function

//aditionally, add error handling on the database, use the exceptionHelper trait
//to set internal exception, and have the endQuery statement deal with it.
//lastly, this might be a cool package to distribute to classmates or something like that.
//although it needs to be refactored to fit within a normal database space.
//for custom module, also include a custom query function. that possibly adhere's to the selectors

//try to make join statements easier to create
//try to make generic crud statements with easy building
//easy prepared statements
//make a software out of this that manages your db for you in js

require_once("vendor/autoload.php");

use config\DatabaseConfig;
use engines\SchemaCreator;
use access\Query;

$database = DatabaseConfig::create([
  "servername" => "localhost",
  "username" => "root",
  "password" => "",
  "useExistingDatabase" => true,
  "databaseName" => "bmbuilder_testing"
])->defineSchema(function(){
  return SchemaCreator::createSchemaWithXmlFile("schema.xml");
});

/*
$database->defineQuery(function($conn){
  Query::start($conn, "test")
  ->insert(["selectors" => ["value"], "values" => ["krijg tering"]])
  ->endQuery();
});
*/

var_dump($database)
?>
<h1>test page for displaying the data</h1>