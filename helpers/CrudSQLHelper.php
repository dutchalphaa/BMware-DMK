<?php
/**
 * @package BMware DMK
 */

namespace helpers;

/**
 * 
 */
trait CrudSQLHelper
{
  protected $components = [];
  protected $table;
  protected $query;

  protected static $lastQuery;

  public static function create(string $table)
  {
    self::encloseBackticks($table);
    
    $query = new static();
    $query->setTable($table);

    return $query;
  }

  public static function getLastQuery()
  {
    return static::$lastQuery;
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function setTable(string $value)
  {
    $this->table = $value;
  }

  protected function encloseBackticks(string &$field)
  {
    $fieldExp = explode(".", $field);
    if(isset($fieldExp[1])){
      $fieldExp[0] = "`" . $fieldExp[0] . "`";
      $fieldExp[1] = "`" . $fieldExp[1] . "`";

      $field = implode(".", $fieldExp);
    } else {
      $field = "`$field`";
    }
  }
}
