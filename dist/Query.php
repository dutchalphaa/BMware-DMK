<?php
/**
 * @package BMware DMK
 */

namespace dist;

use \helpers\BaseQuery;

/**
 * class that holds all the information and functions for creating database queries
 */
class Query extends BaseQuery
{
  /**
   * function that creates the Query object
   *
   * @param   array   $tables - tables to be affected bythe query
   * @return  Query
   */
  public static function start($tables)
  {
    $query = new Query();
    $query->tables = $tables;
    return $query;
  }

  /**
   * function that turns the query into a read/select query
   *
   * @param   array   $toSelect - holds all of the paramaters for the select/read call
   * @return  Query
   */
  public function select(array $toSelect = ["*"])
  {
    //implement schema support later
    $this->components["action"] = "read";
    $this->components["selectors"] = $toSelect;

    return $this;
  }

  /**
   * function that turns the query into a update query
   *
   * @param   array   $toUpdate - holds all of the paramaters for the update call
   * @return  Query
   */
  public function update(array $toUpdate)
  {
    //implement schema support later
    $this->components["action"] = "update";
    $this->components["selectors"] = $toUpdate["selectors"];
    $this->components["values"] = $toUpdate["values"];

    return $this;
  }

  /**
   * function that turns the query into a delete/remove query
   *
   * @return  Query
   */
  public function remove()
  {
    //implement schema support later
    $this->components["action"] = "delete";

    return $this;
  }

  /**
   * function that turns the query into a create/insert query
   *
   * @param   array     $toInsert - holds all of the parameters for the insert/create call
   * @return  Query
   */
  public function insert(array $toInsert)
  {
    //implement schema support later
    $this->components["action"] = "create";
    $this->components["selectors"] = $toInsert["selectors"];
    $this->components["values"] = $toInsert["values"];

    return $this;
  }

  /**
   * function that defines a condition on the query
   *
   * @param   array   $conditions - holds all of the parameters for the where statement
   * @return  Query
   */
  public function where(array $conditions)
  {
    if(!isset($this->components["action"])){
      throw new \Exception("cannot declare where condition before the action");
    }

    $this->components["where"] = $conditions;

    return $this;
  }

  /**
   * function that defines the end of the query, and adds the affected table to the query object
   *
   * @return  Query
   */
  public function endQuery()
  {
    $this->components["tables"] = $this->tables;
    return $this;
  }
}