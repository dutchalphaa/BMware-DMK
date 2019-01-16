<?php
/**
 * @package BMware DMK
 */

namespace dist;

use \config\WordpressDatabaseConfig;
use \engines\MigrationCreator;
use \engines\SchemaEngine;
use \engines\QueryCreator;
use \models\DatabaseSchema;
use \models\DatabaseResult;
use \dist\Migration;
use \dist\Query;

/**
 * class that uses the wordpress wpdb class to make calls to the database
 */
final class WordpressDatabase
{
  /**
   * holds the schema object of the database
   *
   * @var DatabaseSchema
   */
  private $databaseSchema;
  /**
   * variable that indicates wether someone is allowed acces to certain functions
   *
   * @var boolean
   */
  private $access = false;
  /**
   * variable that holds the global wpdb class
   *
   * @var wpdb
   */
  private $conn;
  /**
   * variable that holds the prefix that is chosen by the user
   *
   * @var string
   */
  public $prefix;

  /**
   * initialize some variable for the database object
   *
   * @param   WordpressDatabaseConfig  $config - holds all the config options for the database object
   */
  public function __construct(WordpressDatabaseConfig $config)
  {
    $this->conn = $config->conn;
    $this->prefix = $config->prefix;
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
          $this->$toAccess($arg);
        }
      } else {
        return $this->$toAccess;
      }
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
    try {
      $query = $definition(array($this, "getAccess"));
    }
    catch (\Exception $e){
      $this->access = false;
      echo $e->getMessage();
    }

    if($query instanceof Query){
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
    } else if ($query == null) {
      //do nothing
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
  private function modelDatabaseWithSchema($databaseSchema)
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
   * function that allows you to wrtie pure SQL to the database, use only if truly necisairy.
   * because this will skip some of the optimization
   *
   * @param string $query
   * @return DatabaseResult
   */
  private function excecuteQuery(string $query)
  {
    $result = $this->conn->get_results($query, ARRAY_A);
    
    if(empty($result)){
      $result = "query excecuted, result unknown";
      $count = 0;
    } else {
      $count = count($result);
    }


    return new DatabaseResult($result, $count);
  }
}