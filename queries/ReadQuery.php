<?php
/**
 * @package BMware DMK
 */

namespace queries;

use helpers\BaseCrudQuery;
use helpers\CrudQueryWhereHelper;

class ReadQuery extends BaseCrudQuery
{
  use CrudQueryWhereHelper;

  public function select(string $selector = "*", string ...$selectors)
  {
    if($selector == ""){
      throw new \exceptions\InvalidQueryException("Invalid argument, selector cannot be an empty string");
    }

    if($selector === "*"){
      $this->components["selectors"] = "SELECT * FROM $this->table";

      return $this;
    }
    $selector = $this->encloseBackticks($selector);
    if(empty($selectors)){
      $this->components["selectors"] = "SELECT $selector FROM $this->table";

      return $this;
    }
    $extraSelectors = "";

    foreach ($selectors as $select) {
      $select = $this->encloseBackticks($select);
      $extraSelectors .= ", $select";
    }

    $selector .= $extraSelectors;
    $this->components["selectors"] = "SELECT $selector FROM $this->table";

    return $this;
  }

  public function join(string $table, string $conditionFirstTable, string $conditionSecondTable, bool $outer = true)
  {
    if(!isset($this->components["join"])){
      $this->components["join"] = [];
    }

    $table = $this->encloseBackticks($table);
    ($outer) ? $joinType = "OUTER" : $joinType = "INNER";
    $conditionFirstTable = $this->encloseBackticks($conditionFirstTable);
    $conditionSecondTable = $this->encloseBackticks($conditionSecondTable);

    array_push($this->components["join"], "LEFT $joinType JOIN $table ON $conditionFirstTable = $conditionSecondTable");

    return $this;
  }
  
  public function endQuery(bool $condition = false)
  {
    ($condition) ? $delimiter = "OR" : $delimiter = "AND";;
    if(isset($this->components["where"])){
      $this->components["where"] = implode(" $delimiter ", $this->components["where"]);
    }

    if(isset($this->components["join"])){
      $this->components["join"] = implode(" ", $this->components["join"]);
    }

    
    $this->query = implode(" ", $this->components);
    
    $this->createUnionQuery();
    
    static::$lastQuery = $this; 
    return $this;
  }
}