<?php
/**
 * @package BmEstore
 */

namespace engines;

use \models\DatabaseSchema;

class SchemaCreator 
{
  public $schema;
  public $schemaObject;

  public function __construct()
  {

  }

  public function createDatabaseSchemaObject()
  {
    $tables = [];
    $primaryKeys = [];

    foreach($this->schema as $table => $content){
      //create database objects
      $tables[$table] = $content;
      foreach ($this->schema[$table] as $key => $value) {
        if(strpos($value, "PRIMARY")){
          $primaryKeys[$table] = $key;
        }
      }
    }

    $this->schemaObject = new DatabaseSchema($tables, $primaryKeys);
    return $this->schemaObject;
  }

  public static function createSchemaWithXmlFile($location)
  {
    $schema = new SchemaCreator();
    $schema->schema = \json_decode(\json_encode(\simplexml_load_file($location)), true);
    if($schema->schema == null){
      throw new \Exception("Error loading file");
    }

    return $schema->createDatabaseSchemaObject();
  }
}