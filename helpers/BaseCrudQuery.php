<?php
/**
 * @package BMware DMK
 */

namespace helpers;

abstract class BaseCrudQuery
{
  public $table;

  protected $components = [];
  protected $query;
  
  protected static $unionQueries = [];
  protected static $lastQuery;

  public static function getLastQuery()
  {
    return static::$lastQuery;
  }

  public static function create(string $table)
  {
    $query = new static();
    $query->table = $query->encloseBackticks($table);

    return $query;
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function union(string $table = "")
  {
    $unionQuery = new static();
    
    if($table === ""){
      $unionQuery->table = $this->table;
    } else {
      $unionQuery->table = $this->encloseBackticks($table);
    }


    if(!isset($this->query)){
      $this->endQuery();
    }

    array_push(static::$unionQueries, $this->query);
    return $unionQuery;
  }

  public function encloseBackticks(string $field)
  {
    $fieldExp = explode(".", $field);
    if(isset($fieldExp[1])){
      $fieldExp[0] = "`" . $fieldExp[0] . "`";
      $fieldExp[1] = "`" . $fieldExp[1] . "`";

      $field = implode(".", $fieldExp);
    } else {
      $field = "`$field`";
    }

    return $field;
  }

  protected function createUnionQuery()
  {
    if(!empty(static::$unionQueries)){
      array_push(static::$unionQueries, $this->query);
      $this->query = implode(" UNION ", static::$unionQueries);
      static::$unionQueries = [];
    }
  }

  abstract public function endQuery();
}