<?php
/**
 * @package BMware DMK
 */

namespace dist;

use \engines\MigrationCreator;
use \engines\SchemaEngine;
use \engines\QueryCreator;
use \models\DatabaseSchema;
use \models\DatabaseResult;
use \config\DatabaseConfig;
use \dist\Migration;
use \dist\Query;

/**
 * class that has all of the functionality for making calls to the SQL database
 */
final class Database
{
  /**
   * holds the schema object of the database
   *
   * @var DatabaseSchema
   */
  private $databaseSchema;
  /**
   * holds the name of the database
   *
   * @var string
   */
  private $databaseName;
  /**
   * holds the sql connection variable
   *
   * @var mysqli
   */
  private $conn;
  /**
   * holds a value that represents wether or not the getaccess function can be called
   *
   * @var boolean
   */
  private $access = false;

  /**
   * initialize some variable for the database object
   *
   * @param   DatabaseConfig  $config - holds all the config options for the database object
   */
  public function __construct(DatabaseConfig $config)
  {
    $this->conn = $config->conn;
    $this->databaseName = $config->databaseName;
  }

  /**
   * function that allows you to call certain private methods and properties within define function,
   * it is passed to define as a argument
   *
   * @param   string  $toAccess - name of the function or property to access
   * @param   any     $arg      - argument for the function
   * @return  any
   */
  public function getAccess($toAccess, $arg = null)
  {
    if($this->access) {
      //add a kind of check to see if people can access that given function?
      if(isset($arg)) {
        if(method_exists($this, $toAccess)) {
          return $this->$toAccess($arg);
        }
      } else {
        if($toAccess === "conn"){
          throw new \exceptions\InvalidContextArgumentException();
        }
        return $this->$toAccess;
      }
    } else {
      throw new \exceptions\NoAccessException();
    }
  }
    
  /**
   * function that gives the user access to the database using the Query/Migration object,
   * or alternatively, create a custom query with the excecuteQuery function
   *
   * @param   callable  $definition - custom function that either returns a Query/Migration object,
   * or null if a custom query is excecuted
   * @return  any
   */
  public function define(callable $definition)
  {
    $this->access = true;

    $query = $definition(array($this, "getAccess"));

    if(!isset($query)) {
      //do nothing
    } else if($query instanceof DatabaseResult) {
      return $query;
    } else if($query instanceof Query){
      //conditional logics can be excecuted here;
      
      $query = QueryCreator::createQuery($query->components);
      $result = $this->excecuteQuery($query);
    } else if ($query instanceof Migration) {
      SchemaEngine::updateSchemaWithMigration($query, $this->databaseSchema);

      //conditional logics can be excecuted here;
      $query = MigrationCreator::createQuery($query->components);
      $result = $this->excecuteQuery($query);
    } else if ($query instanceof DatabaseSchema){
      $this->modelDatabaseWithSchema($query);
    } else {
      throw new \Exception("object given was not null or a query, migration or schema");
    }

    if(isset($result)){
      $this->access = false;
      return $result;
    }
    $this->access = false;
    return $this;
  }

  /**
   * function that adds the database schema to the database object
   *
   * @param string|DatabaseSchema  $databaseSchema
   * @return void
   */
  private function notFinished_modelDatabaseWithSchema($databaseSchema)
  {
    if($databaseSchema instanceof DatabaseSchema) {
      $this->databaseSchema = $databaseSchema;
    } else if(is_string($databaseSchema)) {
      $this->databaseSchema = SchemaEngine::createSchemaWithXmlFile($databaseSchema, $this->databaseName);
    } else {
      throw new \Exception("this function expects a xml schema file location or a schema object");
    }
    //migration logics here
  }

  /**
   * Function that excecutes the function on the database, and casts the result into a DatabaseResult object.
   * Can be called manualy however this is advised against.
   *
   * @param string $query
   * @return DatabaseResult
   */
  private function excecuteQuery(string $query)
  {
    $result = \mysqli_query($this->conn, $query);

    if(\mysqli_error($this->conn)){
      throw new \exceptions\InvalidQueryException("Query invalid, here's what went wrong: " . \mysqli_error($this->conn));
    }
    $queryResult = '';
    $numrows = 0;

    //cast into a database result object
    if(is_bool($result)){
      if($result) {
        $queryResult = "row(s) successfully added/altered";
      }else{
        $queryResult = "row(s) not added/altered";
      }
    }else {
      $rows = [];
  
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          array_push($rows, $row);
        }

        $queryResult = $rows;
        $numrows = $result->num_rows;

      } else {
        $queryResult = "0 results";
      }
    }
    return new DatabaseResult($queryResult, $numrows);
  }
}