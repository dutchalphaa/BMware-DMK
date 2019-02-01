<?php
/**
 * @package BMware DMK
 */

namespace helpers;

abstract class BaseCrudQuery extends BaseCrudSQL
{
  protected $preparedTypes = "";
  protected $variables = [];
  
  protected static $unionQueries = [];

  public function getVariables()
  {
    return $this->variables;
  }

  public function getPreparedTypes()
  {
    return $this->preparedTypes;
  }

  public function union(string $table = "")
  {
    $unionQuery = new static();
    
    if($table !== ""){
      $this->encloseBackticks($table);
    }
    
    $unionQuery->table = $table;

    if(!isset($this->query)){
      $this->endQuery();
    }

    array_push(static::$unionQueries, $this);
    return $unionQuery;
  }

  public function encloseBackticks(string &$field)
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

  protected function createUnionQuery()
  {
    if(!empty(static::$unionQueries)){

      $variables = [];
      $preparedTypes = "";
      $unionQueries = [];

      foreach(static::$unionQueries as $query)
      {
        array_push($variables, ...$query->getVariables());
        $preparedTypes .= $query->getPreparedTypes();
        array_push($unionQueries, $query->getQuery());
      }

      array_push($variables, ...$this->variables);
      $this->preparedTypes = $preparedTypes . $this->preparedTypes;
      array_push($unionQueries, $this->query);

      $this->variables = $variables;

      $this->query = implode(" UNION ", $unionQueries);
      static::$unionQueries = [];
    }
  }

  protected function isStringIntDouble(...$variables)
  {
    foreach ($variables as $variable) {
      if(!is_string($variable) && !is_int($variable) && !is_double($variable)){
        throw new \InvalidArgumentException("Argument has to be of type string, int or double");
      }
    }
  }

  protected function returnTypeStringIntDouble($variable)
  {
    if(is_string($variable)){
      return "s";
    } else if (is_int($variable)) {
      return "i";
    } else if (is_double($variable)) {
      return "d";
    } else {
      throw new \InvalidArgumentException("Argument has to be of type string, int, or double");
    }
  }

  abstract public function endQuery();
}