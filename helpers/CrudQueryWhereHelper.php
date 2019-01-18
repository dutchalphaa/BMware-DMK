<?php
/**
 * @package BMware DMK
 */

namespace helpers;

trait CrudQueryWhereHelper
{
  public function whereEquals(string $field, string $value, bool $notEquals = false)
  {
    $firstWhere = false;
    $field = $this->encloseBackticks($field);
    $value = "'$value'";
    ($notEquals) ? $condition = "!=" : $condition = "=";
    
    if(!isset($this->components["where"])){
      $firstWhere = true;
      $this->components["where"] = [];
    }

    ($firstWhere) ? $field = "WHERE $field" : false;
    array_push($this->components["where"], "$field $condition $value");
    return $this;
  }

  public function whereGreaterThan(string $field, string $value, bool $orEqualTo = false)
  {
    $firstWhere = false;
    $condition = ">";
    ($orEqualTo) ? $condition .= "=" : false;
    $field = $this->encloseBackticks($field);
    $value = "'$value'";
    
    if(!isset($this->components["where"])){
      $firstWhere = true;
      $this->components["where"] = [];
    
    }
    ($firstWhere) ? $field = "WHERE $field" : false;
    array_push($this->components["where"], "$field $condition $value");
    return $this;
  }

  public function whereLessThan(string $field, string $value, bool $orEqualTo = false)
  {
    $firstWhere = false;
    $condition = "<";
    ($orEqualTo) ? $condition .= "=" : false;
    $field = $this->encloseBackticks($field);
    $value = "'$value'";
    
    if(!isset($this->components["where"])){
      $firstWhere = true;
      $this->components["where"] = [];
    }
    
    ($firstWhere) ? $field = "WHERE $field" : false;
    array_push($this->components["where"], "$field $condition $value");
    return $this;
  }
}