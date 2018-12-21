<?php
/**
 * @package BMware DMK
 */

namespace models;

class TableSchema
{
  public $fields;
  public $name;

  public function __construct($name, $fields)
  {
    $this->name = $name;
    $this->fields = $fields;
  }

  public function fieldCount()
  {
    return \count($fields);
  }
}