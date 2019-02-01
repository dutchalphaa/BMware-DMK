<?php
/**
 * @package BMware DMK
 */

namespace helpers;

use models\DatabaseResult;
use models\Schema;
use schema\SchemaManager;

abstract class BaseDatabase
{
  /**
   * holds the schema object of the database
   *
   * @var Schema
   */
  protected $schema;
  /**
   * variable that holds the connection to a database
   *
   * @var any
   */
  protected $conn;
  /**
   * variable that indicates wether someone is allowed acces to certain functions
   *
   * @var boolean
   */
  protected $access = false;
  protected $databaseName;

  /**
   * function that allows you to call certain private methods and properties within define function,
   * it is passed to define as a argument
   *
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
   * @param   any  $definition - custom function that either returns a Query/Migration object,
   * or null if a custom query is excecuted
   * @return  any
   */
  public function define($definition)
  {
    $this->access = true;

    if(is_callable($definition)){
      $query = $definition(array($this, "getAccess"));
    } else if(is_subclass_of($definition, BaseCrudQuery::class)) {
      $this->access = false;
      return $this->executeQuery($definition);
    }

    $this->access = false;

    if($query instanceof DatabaseResult) {
      return $query;
    } else if(is_subclass_of($query, BaseCrudQuery::class)){
      return $this->executeQuery($query);
    } else if(!isset($query)) {
      return $this;
    } else {
      throw new \exceptions\InvalidDefineReturnType();
    }
  }

  public function useSchema(string $pathToFile = "")
  {
    $schemaManager = new SchemaManager($this);

    if($pathToFile === ""){
      $schemaManager->createSchema($this->databaseName);
    } else {
      $schemaManager->loadSchema($pathToFile, $this->databaseName);
    }

    $this->schema = $schemaManager->getNewSchema();
    if(!$this->access){
      return $this;  
    }
  }

  public function getSchema()
  {
    var_dump($this->schema);
    return $this;
  }

  abstract protected function executeQuery(string $query);
}