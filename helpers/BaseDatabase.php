<?php
/**
 * @package BMware DMK
 */

namespace helpers;

use models\DatabaseSchema;
use models\DatabaseResult;
use queries\CreateQuery;
use queries\ReadQuery;
use queries\UpdateQuery;
use queries\DeleteQuery;


abstract class BaseDatabase
{
  /**
   * holds the schema object of the database
   *
   * @var DatabaseSchema
   */
  protected $databaseSchema;
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
   * @param   callable  $definition - custom function that either returns a Query/Migration object,
   * or null if a custom query is excecuted
   * @return  any
   */
  public function define($definition)
  {
    $this->access = true;

    if(is_callable($definition)){
      $query = $definition(array($this, "getAccess"));
    } else if(is_subclass_of($definition, BaseCrudQuery::class)) {
      return $this->executeQuery($definition);
    }

    if(!isset($query)) {
      //do nothing
    } else if($query instanceof DatabaseResult) {
      return $query;
    } else if(is_subclass_of($query, BaseCrudQuery::class)){
      $result = $this->executeQuery($query);
    } else if ($query instanceof Migration) {
      //do nothing yet
    } else if ($query instanceof DatabaseSchema){
      //do nothing yet
    } else {
      throw new \exceptions\InvalidDefineReturnType();
    }

    if(isset($result)){
      $this->access = false;
      return $result;
    }
    $this->access = false;
    return $this;
  }

  protected function prepareStatement($query)
  {
    if(!is_subclass_of($query, BaseCrudQuery::Class)){
      return;
    }

    

    return $query;
  }

  /**
   * function that adds the database schema to the database object
   *
   * @param string|DatabaseSchema  $databaseSchema
   * @return void
   */
  protected function notFinished_modelDatabaseWithSchema($databaseSchema)
  {
    if($databaseSchema instanceof DatabaseSchema) {
      $this->databaseSchema = $databaseSchema;
    } else if(is_string($databaseSchema)) {
      $this->databaseSchema = SchemaEngine::createSchemaWithXmlFile($databaseSchema, $this->databaseName);
    } else {
      //make custom expception once this gets implemented
      throw new \Exception("this function expects a xml schema file location or a schema object");
    }
    //migration logics here
  }

  abstract protected function executeQuery(string $query);
}