<?php
/**
 * @package BmBuilder
 */

namespace models;

class DatabaseSchema
{
  public $tables;
  public $primaryKeys;

  public function __construct($tables, $primaryKeys )
  {
    $this->tables = $tables;
    $this->primaryKeys = $primaryKeys;
  }

  public function tableCount()
  {
    return \count($this->tables);
  }
}