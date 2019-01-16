<?php
/**
 * @package BMware DMK
 */

namespace engines;

use \models\DatabaseSchema;
use \models\TableSchema;
use \dist\Migration;

class SchemaEngine 
{
  public $schema;
  public $schemaObject;

  public static function createXmlFileWithSchema(string $location, DatabaseSchema $schema)
  {
    $xml = new \DOMDocument("1.0", "utf-8");
    $xml->formatOutput = true;

    $database = $xml->createElement($schema->databaseName);
    $xml->appendChild($database);

    foreach ($schema->tables as $table => $schemaObject) {
      $xmlTable = $xml->createElement($table);
      foreach ($schemaObject->fields as $key => $value) {
        $xmlField = $xml->createElement($key, $value);
        $xmlTable->appendChild($xmlField);
      }

      $database->appendChild($xmlTable);
    }

    $xml->save($location);
  }

  public static function createSchemaWithXmlFile(string $location, string $databaseName)
  {
    $schema = new SchemaEngine();
    $schema->schema = \json_decode(\json_encode(\simplexml_load_file($location)), true);
    if($schema->schema == null){
      throw new \Exception("Error loading file");
    }

    return $schema->createDatabaseSchemaObject($location, $databaseName);
  }

  public static function updateSchemaWithMigration(Migration $migration, DatabaseSchema &$schema)
  {
    switch ($migration->components["action"]) {
      case "create":
        $schemaEngine = new SchemaEngine();
        $schemaEngine->updateXmlCreate($schema, $migration->components);
        break;

      case "update":
        $schemaEngine = new SchemaEngine();
        $schemaEngine->updateXmlUpdate($schema, $migration->components);
        break;
    }
  }

  public static function updateDatabaseWithSchema(DatabaseSchema $schema)
  {
    
  }

  public function createDatabaseSchemaObject($location = null, string $databaseName)
  {
    $table = [];
    $tableSchemas = [];
    $primaryKeys = [];

    foreach($this->schema as $table => $content){
      //create database objects
      $tables[$table] = $content;
      $tableSchemas[$table] = new TableSchema($table, $content);
      
      foreach ($this->schema[$table] as $key => $value) {
        if(strpos($key, "primary") !== false){
          $primaryKeys[$table] = $value;
        }
      }
    }

    $this->schemaObject = new DatabaseSchema($tableSchemas, $primaryKeys, $databaseName);
    if($location != null){
      $this->schemaObject->schemaFile = $location;
    }
    return $this->schemaObject;
  }

  public function updateXmlCreate(DatabaseSchema &$schema, array $components)
  {
    $schema->tables[$components["tables"][0]] = new TableSchema($components["tables"][0], $components["selectors"]);

    SchemaEngine::createXmlFileWithSchema($schema->schemaFile, $schema);
  }

  public function updateXmlUpdate(DatabaseSchema &$schema, array $components)
  {
    if(empty($components["selectors"])){
      $schema->tables[$components["tables"][0]]->name = $components["values"];
      $this->changeArrayKeys($schema->tables, $components["tables"][0], $components["values"]);
    } else {
      if(is_array($components["values"])) {
        //do logic for change
        $newFieldName = array_keys($components["values"])[0];
        $schema->tables[$components["tables"][0]]->fields[$components["selectors"]] = $components["values"][$newFieldName];
        $this->changeArrayKeys($schema->tables[$components["tables"][0]]->fields, $components["selectors"], $newFieldName);
      }else {
        //do Logic for modify
        $schema->tables[$components["tables"][0]]->fields[$components["selectors"]] = $components["values"];
      }
    }
    
    SchemaEngine::createXmlFileWithSchema($schema->schemaFile, $schema);
  }

  public function updateXmlDelete(DatabaseSchema &$schema, array $components)
  {

  }

  private function changeArrayKeys(array &$array, string $oldKey, string $newKey)
  {
    if(!array_key_exists( $oldKey, $array )){
      echo "hello";
      return $array;
    }

    $keys = array_keys( $array );
    $keys[ array_search( $oldKey, $keys ) ] = $newKey;

    $array = array_combine( $keys, $array );
  }
}