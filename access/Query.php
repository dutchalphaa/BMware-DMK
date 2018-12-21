<?php
/**
 * @package BMware DMK
 */

namespace access;

use \helpers\BaseQuery;
use \helpers\IBaseQuery;

class Query extends BaseQuery
{
  public static function start($tables)
  {
    $query = new Query();
    $query->tables = $tables;
    return $query;
  }

  //some sql helpers
  public function select(array $toSelect = ["*"])
  {
    //implement schema support later
    $this->components["action"] = "read";
    $this->components["selectors"] = $toSelect;

    return $this;
  }

  public function update(array $toUpdate)
  {
    //implement schema support later
    $this->components["action"] = "update";
    $this->components["selectors"] = $toUpdate["selectors"];
    $this->components["values"] = $toUpdate["values"];

    return $this;
  }

  public function remove()
  {
    //implement schema support later
    $this->components["action"] = "delete";

    return $this;
  }

  public function insert(array $toInsert)
  {
    //implement schema support later
    $this->components["action"] = "create";
    $this->components["selectors"] = $toInsert["selectors"];
    $this->components["values"] = $toInsert["values"];

    return $this;
  }

  public function where(array $conditions)
  {
    if(!isset($this->components["action"])){
      throw new \Exception("cannot declare where condition before the action");
    }

    $this->components["where"] = $conditions;

    return $this;
  }

  public function conditional(array $conditionals)
  {
    if(!isset($this->components["action"])){
      throw new \Exception("cannot declare conditional statement before the action");
    }
    //create some functions that check certain things in the database
    $this->components["conditionals"] = $conditionals;

    return $this;
  }

  public function endQuery()
  {
    $this->components["tables"] = $this->tables;
    return $this;
  }
}