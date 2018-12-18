<?php
/**
 * @package BmEstore
 */

namespace engines;

use \models\DatabaseSchema;
use \models\TableSchema;

class SchemaCreator 
{
  public $schema;
  public $schemaObject;

  public function __construct()
  {

  }

  public function createDatabaseSchemaObject($location)
  {
    $table = [];
    $tableSchemas = [];
    $primaryKeys = [];

    foreach($this->schema as $table => $content){
      //create database objects
      $tables[$table] = $content;
      $tableSchemas[$table] = new TableSchema($content);
      
      foreach ($this->schema[$table] as $key => $value) {
        if(strpos($value, "PRIMARY")){
          $primaryKeys[$table] = $key;
        }
      }
    }

    $this->schemaObject = new DatabaseSchema($tableSchemas, $primaryKeys, $location);
    return $this->schemaObject;
  }

  public static function createSchemaWithXmlFile($location)
  {
    $schema = new SchemaCreator();
    $schema->schema = \json_decode(\json_encode(\simplexml_load_file($location)), true);
    if($schema->schema == null){
      throw new \Exception("Error loading file");
    }

    return $schema->createDatabaseSchemaObject($location);
  }
}