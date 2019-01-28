<?php
/**
 * @package BMware DMK
 */

namespace queries;

use helpers\BaseCrudQuery;

class CreateQuery extends BaseCrudQuery
{
  public function select(string $selector, string ...$selectors)
  {
    if($selector == ""){
      throw new \exceptions\InvalidQueryException("Invalid argument, selector cannot be an empty string");
    }

    $selector = $this->encloseBackticks($selector);
    if(empty($selectors)){
      $this->components["selectors"] = "INSERT INTO $this->table ( $selector ) VALUES";
      
      return $this;
    }

    $extraSelectors = "";

    foreach($selectors as $select){
      $select = $this->encloseBackticks($select);
      $extraSelectors .= ", $select";
    }

    $selector .= $extraSelectors;
    $this->components["selectors"] = "INSERT INTO $this->table ( $selector ) VALUES";

    return $this;
  }

  public function values($value, ...$values)
  {
    $this->isStringIntDouble($value, ...$values);

    if(!isset($this->components["values"])){
      $this->components["values"] = [];
    }

    if(empty($values)){
      array_push($this->variables, $value);
      $this->preparedTypes .= $this->returnTypeStringIntDouble($value);
      array_push($this->components["values"], "( ? )");
      return $this;
    }
    
    array_push($this->variables, $value);
    $this->preparedTypes .= $this->returnTypeStringIntDouble($value);
    $value = "?";

    $extraValues = "";
    foreach ($values as $val) {
      array_push($this->variables, $val);
      $this->preparedTypes .= $this->returnTypeStringIntDouble($val);
      $extraValues .= ", ?";
    }
  
    $value .= $extraValues;
    array_push($this->components["values"], "( $value )");
    return $this;
  }

  public function endQuery()
  {
    $this->components["values"] = implode(", ", $this->components["values"]);

    $this->query = implode(" ", $this->components);

    $this->createUnionQuery();

    static::$lastQuery = $this;
    return $this;
  }
}