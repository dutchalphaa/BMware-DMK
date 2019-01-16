<?php
/**
 * @package BMware DMK
 */

namespace models;

/**
 * class that holds the database schema as an object
 */
class DatabaseSchema
{
  /**
   * holds the location of the schemafile
   *
   * @var   string
   */
  public $schemaFile = "none";
  /**
   * holds the name of the database
   *
   * @var   string
   */
  public $databaseName;
  /**
   * holds primary keys of all of the tables
   *
   * @var   array
   */
  public $primaryKeys;
  /**
   * holds all of the tables
   *
   * @var   array
   */
  public $tables;

  /**
   * initialize all of the variables
   *
   * @param   array   $tables
   * @param   array   $primaryKeys
   * @param   string  $databaseName
   */
  public function __construct($tables, $primaryKeys, $databaseName)
  {
    $this->tables = $tables;
    $this->primaryKeys = $primaryKeys;
    $this->databaseName = $databaseName;
  }

  /**
   * function that returns the amount of tables
   *
   * @return int
   */
  public function tableCount()
  {
    return \count($this->tables);
  }
}