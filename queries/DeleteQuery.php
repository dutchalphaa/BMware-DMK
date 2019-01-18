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

  public function __construct()
  {
    $this->components["selectors"] = "DELETE FROM";
  }

  public function endQuery(bool $condition = false)
  {
    $this->components["selectors"] .= " $this->table";
    ($condition) ? $delimiter = "OR" : $delimiter = "AND";
    if(isset($this->components["where"])){
      $this->components["where"] = implode(" ", $this->components["where"]);
    }

    $this->query = implode(" $delimiter ", $this->components);

    $this->createUnionQuery();

    static::$lastQuery = $this;
    return $this;
  }
}