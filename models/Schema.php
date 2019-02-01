<?php
/**
 * @package BMware DMK
 */

namespace models;

class Schema implements \IteratorAggregate
{
  protected $tableStructure;
  protected $databaseName;
  protected $primaryKeys;
  protected $tableCount;
  protected $tables;

  public function __construct(array $tableStructure, string $databaseName, array $primaryKeys, int $tableCount, array $tables)
  {
    $this->tableStructure = $tableStructure;
    $this->databaseName = $databaseName;
    $this->primaryKeys = $primaryKeys;
    $this->tableCount = $tableCount;
    $this->tables = $tables;
  }

  public function getIterator()
  {
    return new \ArrayIterator($this->tables);
  }

  public function getTableStructure()
  {
    return $this->tableStructure;
  }

  public function getDatabaseName()
  {
    return $this->databaseName;
  }

  public function getPrimaryKeys()
  {
    return $this->primaryKeys;
  }

  public function getTableCount()
  {
    return $this->tableCount;
  }

  public function getTables()
  {
    return $this->tables;
  }
}