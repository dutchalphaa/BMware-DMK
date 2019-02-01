<?php
/**
 * @package BMware DMK
 */

namespace schema;

use Symfony\Component\Yaml\Yaml;
use models\Schema;

class SchemaManager
{
  protected $database;
  protected $currentSchema;
  protected $newSchema;
  protected $parsedYml;

  public function __construct($database)
  {
    $this->database = $database;
  }

  public function loadSchema(string $pathToFile, string $databaseName)
  {
    $this->parsedYml = Yaml::parseFile($pathToFile);
    $tableStructure = array_keys($this->parsedYml[$databaseName]);
    $tables = $this->parsedYml[$databaseName];
    $tableCount = count($tableStructure);
    $primaryKeys = [];

    foreach ($this->parsedYml[$databaseName] as $table => $column) {
      foreach ($column as $field => $attributes) {
        if($attributes["primary"] === true){
          array_push($primaryKeys, "$table.$field");
        }
      }
    }

    $this->newSchema = new Schema($tableStructure, $databaseName, $primaryKeys, $tableCount, $tables);
  }

  public function createSchema(string $databaseName)
  {
    //do some read database logics to find out all tables
    //create a database schema based on those
    //create in file designated by the user
  }

  public function updateSchema()
  {
    //update internal array structure
    //dump contents in file
  }

  public function getNewSchema()
  {
    return $this->newSchema;
  }
}