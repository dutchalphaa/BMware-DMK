<?php
/**
 * @package BMware DMK
 */

namespace access;

use \engines\MigrationCreator;
use \engines\SchemaEngine;
use \engines\QueryCreator;
use \models\DatabaseSchema;
use \config\DatabaseConfig;
use \access\Migration;
use \access\Query;

final class Database
{
  private $databaseSchema;
  private $config;
  private $conn;
  private $access = false;


  public function __construct(DatabaseConfig $config)
  {
    $this->config = $config;
    $this->conn = $config->conn;
  }

  public function getAccess($toAccess, $arg = null)
  {
    if($this->access) {
      //add a kind of check to see if people can access that given function?
      if(isset($arg)) {
        if(method_exists($this, $toAccess)) {
          $this->$toAccess($arg);
        }
      } else {
        return $this->$toAccess;
      }
    }
  }
    
  public function define(callable $definition)
  {
    $this->access = true;
    try {
      $query = $definition(array($this, "getAccess"));
    }
    catch (\Exception $e){
      $this->access = false;
      echo $e->getMessage();
    }

    if($query instanceof Query){
      $query = QueryCreator::createQuery($query->components);
      //conditional logics can be excecuted here;
      $result = $this->excecuteQuery($query);
    } else if ($query instanceof Migration) {
      $query = MigrationCreator::createQuery($query->components);

      //conditional logics can be excecuted here;
      $result = $this->excecuteQuery($query);
    } else if ($query instanceof DatabaseSchema){
      $this->modelDatabaseWithSchema($query);
    } else if ($query == null) {
      //do nothing
    } else {
      throw new \Exception("object given was not null or a query, migration or schema");
    }

    if(isset($result)){
      if(!isset($this->databaseSchema)) {
        \mysqli_close($this->conn);
      }
      $this->access = false;
      return $result;
    }
    $this->access = false;
    return $this;
  }

  private function modelDatabaseWithSchema($databaseSchema)
  {
    if($databaseSchema instanceof DatabaseSchema) {
      $this->databaseSchema = $databaseSchema;
    } else if(is_string($databaseSchema)) {
      $this->databaseSchema = SchemaEngine::createSchemaWithXmlFile($databaseSchema);
    } else {
      throw new \Exception("this function expects a xml schema file location or a schema object");
    }
    //migration logics here
  }

  private function excecuteQuery(string $query)
  {
    $result = \mysqli_query($this->conn, $query);

    if(\mysqli_error($this->conn)){
      throw new \Exception("Query invalid, here's what went wrong: " . \mysqli_error($this->conn));
    }
    //cast into a database result object
    var_dump($result);
  }
}