<?php
/**
 * @package BMware DMK
 */

namespace models;

class TableSchema
{
  public $fields;
  public $primaryKey;
  public $name;

  public function __construct($name, $fields)
  {
    $this->name = $name;
    $this->fields = $fields;
    foreach ($fields as $key => $value) {
      if(strpos($key, "primary") !== false) {
        $this->primaryKey = $value;
      }
    }
  }

  public function fieldCount()
  {
    return \count($fields);
  }
}