<?php
/**
 * @package BMware DMK
 */

namespace models;

/**
 * class that holds the table schema as an object
 */
class TableSchema
{
  /**
   * holds the fields of the table
   *
   * @var array
   */
  public $fields;
  /**
   * holds the name of the table
   *
   * @var string
   */
  public $name;

  public function __construct($name, $fields)
  {
    $this->name = $name;
    $this->fields = $fields;
  }

  /**
   * function that returns the amount of fields on the table
   *
   * @return int
   */
  public function fieldCount()
  {
    return \count($fields);
  }
}