<?php
/**
 * @package BmBuilder
 */

namespace models;

class TableSchema
{
  public $fields;
  public $primaryKey;

  public function __construct($fields)
  {
    $this->fields = $fields;
    foreach ($fields as $key => $value) {
      if(strpos($value, "PRIMARY")){
        $this->primaryKey = $key;
      }
    }
  }

  public function fieldCount()
  {
    return \count($fields);
  }
}