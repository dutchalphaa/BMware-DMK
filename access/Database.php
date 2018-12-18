<?php
/**
 * @package BmBuilder
 */

namespace access;

use \config\DatabaseConfig;
use \models\DatabaseSchema;
use \engines\MigrationCreator;
use \engines\QueryCreator;
use \access\Migration;
use \access\Query;

class Database
{
  private $databaseSchema;
  private $config;
  private $conn;

  public function __construct(DatabaseConfig $config)
  {
    $this->config = $config;
    $this->conn = $config->conn;
  }

  public function define(callable $definition)
  {
    try {
      $query = $definition(array($this, "modelDatabaseWithSchema"));
    }
    catch (\Exception $e){
      echo $e->getMessage();
    }

    if($query instanceof Query){
      $query = QueryCreator::createQuery($query->components);
      //conditional logics can be excecuted here;
      $result = Query::excecuteQuery($this->conn, $query);
    } else if ($query instanceof Migration) {
      $query = MigrationCreator::createQuery($query->components);
      //conditional logics can be excecuted here;
      $result = Migration::excecuteQuery($this->conn, $query);
    } else if ($query instanceof DatabaseSchema){
      $this->modelDatabaseWithSchema($query);
    } else {
      throw new \Exception("object given was not a query, migration or schema");
    }

    if(isset($result)){
      if(!isset($this->databaseSchema)) {
        \mysqli_close($this->conn);
      }
      return $result;
    }
    return $this;
  }

  private function modelDatabaseWithSchema(DatabaseSchema $databaseSchema)
  {
    $this->databaseSchema = $databaseSchema;
    //store a copy of the old database

    //do a migration for the database schema
    
    //update/create existing schema files

    //log files migration
  }
}