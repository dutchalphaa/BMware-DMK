<?php
/**
 * @package BMware DMK
 */

namespace queries;

use helpers\BaseCrudQuery;
use helpers\CrudQueryWhereHelper;

class DeleteQuery extends BaseCrudQuery
{
  use CrudQueryWhereHelper;

  protected $extraTables = [];

  public function __construct()
  {
    $this->components["selectors"] = "DELETE FROM";
  }

  public function join(string $table, string $conditionFirstTable, string $conditionSecondTable)
  {
    if(!isset($this->components["join"])){
      $this->components["join"] = [];
    }
    $this->encloseBackticks($table);
    array_push($this->components["join"], $table);

    if(!isset($this->components["where"])){
      $this->components["where"] = [];
    }


    $this->encloseBackticks($conditionFirstTable);
    $this->encloseBackticks($conditionSecondTable);
    array_push($this->components["where"], "WHERE $conditionFirstTable = $conditionSecondTable");

    return $this;
  }

  public function endQuery(bool $condition = false)
  {
    ($condition) ? $delimiter = "OR" : $delimiter = "AND";
    
    if(isset($this->components["join"])){
      $this->components["selectors"] = "DELETE $this->table, " . implode(", ", $this->components["join"]) . " FROM $this->table ";
      foreach ($this->components["join"] as &$join) {
        $join = "JOIN $join";
      }
      $this->components["join"] = implode(", ", $this->components["join"]);
      $delimiter = "AND";
    } else {
      $this->components["selectors"] .= " $this->table";
    }

    if(isset($this->components["where"])){
      $this->components["where"] = implode(" $delimiter ", $this->components["where"]);
    }

    $this->query = implode(" ", $this->components);

    $this->createUnionQuery();

    static::$lastQuery = $this;
    return $this;
  }
}