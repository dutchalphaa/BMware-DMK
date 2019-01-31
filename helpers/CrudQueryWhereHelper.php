<?php
/**
 * @package BMware DMK
 */

namespace helpers;

trait CrudQueryWhereHelper
{
  public function whereEquals(string $field, $value, bool $notEquals = false)
  {
    $this->isStringIntDouble($value);
    array_push($this->variables, $value);
    $this->preparedTypes .= $this->returnTypeStringIntDouble($value);

    $firstWhere = false;
    $this->encloseBackticks($field);
    $value = "'$value'";
    ($notEquals) ? $condition = "!=" : $condition = "=";
    
    if(!isset($this->components["where"])){
      $firstWhere = true;
      $this->components["where"] = [];
    }

    ($firstWhere) ? $field = "WHERE $field" : false;
    array_push($this->components["where"], "$field $condition ?");
    return $this;
  }

  public function whereGreaterThan(string $field, $value, bool $orEqualTo = false)
  {
    $this->isStringIntDouble($value);
    array_push($this->variables, $value);
    $this->preparedTypes .= $this->returnTypeStringIntDouble($value);

    $firstWhere = false;
    $condition = ">";
    ($orEqualTo) ? $condition .= "=" : false;
    $this->encloseBackticks($field);
    $value = "'$value'";
    
    if(!isset($this->components["where"])){
      $firstWhere = true;
      $this->components["where"] = [];
    
    }
    ($firstWhere) ? $field = "WHERE $field" : false;
    array_push($this->components["where"], "$field $condition ?");
    return $this;
  }

  public function whereLessThan(string $field, $value, bool $orEqualTo = false)
  {
    $this->isStringIntDouble($value);
    array_push($this->variables, $value);
    $this->preparedTypes .= $this->returnTypeStringIntDouble($value);

    $firstWhere = false;
    $condition = "<";
    ($orEqualTo) ? $condition .= "=" : false;
    $this->encloseBackticks($field);
    $value = "'$value'";
    
    if(!isset($this->components["where"])){
      $firstWhere = true;
      $this->components["where"] = [];
    }
    
    ($firstWhere) ? $field = "WHERE $field" : false;
    array_push($this->components["where"], "$field $condition ?");
    return $this;
  }
}