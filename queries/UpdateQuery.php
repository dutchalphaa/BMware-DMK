<?php
/**
 * @package BMware DMK
 */

namespace queries;

use helpers\BaseCrudQuery;
use helpers\CrudQueryWhereHelper;

class UpdateQuery extends BaseCrudQuery
{
  use CrudQueryWhereHelper;

  protected $selectors;
  protected $values;

  public function select(string $selector, string ...$selectors)
  {
    if($selector == ""){
      throw new \exceptions\InvalidQueryException("Invalid argument, selector cannot be an empty string");
    }

    $this->components["base"] = "UPDATE $this->table SET";
    $this->components["selectors"] = "";

    if(!empty($selectors)){
      $this->selectors = [];
      array_push($this->selectors, $selector, ...$selectors);
    } else {
      $this->selectors = $selector;
    }

    return $this;
  }

  public function values(string $value, string ...$values)
  {
    if($value == ""){
      throw new \exceptions\InvalidQueryException("Invalid argument, selector cannot be an empty string");
    }

    if(!empty($values)){
      $this->values = [];
      array_push($this->values, $value, ...$values);
    } else {
      $this->values = $value;
    }

    return $this;
  }

  public function endQuery(bool $condition = false)
  {
    if(!is_array($this->selectors) && !is_array($this->values)){
      $this->encloseBackticks($this->selectors);
      $this->values = "'$this->values'";
      $this->components["selectors"] = "$this->selectors = $this->values";
    } else if (count($this->selectors) !== count($this->values)){
      throw new \Exception("cannot have more selectors then values or the other way around");
    } else {
      
      $this->encloseBackticks($this->selectors[0]);
      $selector = $this->selectors[0];
      $value = "'" . $this->values[0] . "'";

      $setClause = "$selector = $value";
      for ($i=1; $i < count($this->selectors); $i++) { 
        $this->encloseBackticks($this->selectors[$i]);
        $select = $this->selectors[$i];
        $val = "'" . $this->values[$i] . "'";

        $setClause .= ", $select = $val";
      }

      $this->components["selectors"] = $setClause;
    }

    ($condition) ? $delimiter = "OR" : $delimiter = "AND";;
    if(isset($this->components["where"])){
      $this->components["where"] = implode(" $delimiter ", $this->components["where"]);
    }

    $this->query = implode(" ", $this->components);

    $this->createUnionQuery();

    static::$lastQuery = $this;
    return $this;
  }
}