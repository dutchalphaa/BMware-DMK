<?php
/**
 * @package BmBuilder
 */

namespace models;

class DatabaseSchema
{
  public $tables;
  public $primaryKeys;
  public $schemaFile;

  public function __construct($tables, $primaryKeys, string $location )
  {
    $this->tables = $tables;
    $this->primaryKeys = $primaryKeys;
    $this->schemaFile = $location;
  }

  public function tableCount()
  {
    return \count($this->tables);
  }
}