<?php
//split into different traits and classes

//done for now

//Database - the object representing the database your conected to
//DatabaseQuery - the object representing the query you make
//DatabaseMigration - the object representing the migration query
//DatabaseSetup - the object that sets up the database and connection
//Database/TableSchema - the objects representing their respective schema's

//Query interpreter - "algorythm" that create's a SQL statement from the Query object

//to do
//Migration interpreter = "algorythm" that create's the SQL statement from the Migration object and excecutes the neccisairy steps for a migration

//variableHelper - sets all the variables, will be called in the constructor function
//selectionHelper - will hold all of the selector functions.
//functionalHelper - holds all helper functions, like the database foreach(WIP) and reset function
//exceptionHelper - holds all of the exception logic that will be passed to endQuery

//DatabaseResult - new class, that will hold all of the data for the results
//IDatabaseCustomAcess - interface for defining custom database access function

//aditionally, add error handling on the database, use the exceptionHelper trait
//to set internal exception, and have the endQuery statement deal with it.
//lastly, this might be a cool package to distribute to classmates or something like that.
//although it needs to be refactored to fit within a normal database space.
//for custom module, also include a custom query function. that possibly adhere's to the selectors

//try to make join statements easier to create
//try to make generic crud statements with easy building
//easy prepared statements
//make a software out of this that manages your db for you.


require_once("vendor/autoload.php");

use config\DatabaseConfig;
use access\Migration;
use access\Query;

$database = DatabaseConfig::create([
  "servername" => "localhost",
  "username" => "root",
  "password" => "",
  "useExistingDatabase" => true,
  "databaseName" => "bmbuilder_testing"
])->define(function($context){
  $context("modelDatabaseWithSchema", "schema.xml");
});


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

?>
<h1>test page for displaying the data</h1>