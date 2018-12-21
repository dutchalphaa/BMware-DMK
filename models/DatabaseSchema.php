<?php
/**
 * @package BMware DMK
 */

namespace models;

class DatabaseSchema
{
  public $schemaFile = "none";
  public $databaseName;
  public $primaryKeys;
  public $tables;

  public function __construct($tables, $primaryKeys, $databaseName)
  {
    $this->tables = $tables;
    $this->primaryKeys = $primaryKeys;
    $this->databaseName = $databaseName;
  }

  public function tableCount()
  {
    return \count($this->tables);
  }
}