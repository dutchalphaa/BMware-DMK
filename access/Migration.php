<?php
/**
 * @package BMware DMK
 */

namespace access;

use \models\DatabaseSchema;
use \helpers\BaseQuery;
use \helpers\IBaseQuery;

/**
 * class that holds all the information and functions for creating migration queries
 */
class Migration extends BaseQuery
{
  /**
   * function that creates the migration Object
   *
   * @param   array           $tables - tables to be affected by the migration
   * @param   DatabaseSchema  $schema - current schema of the database
   * @return  Migration
   */
  public static function start(array $tables, DatabaseSchema $schema)
  {
    $migration = new Migration();
    $migration->tables = $tables;
    $migration->schema = $schema;
    return $migration;
  }

  /**
   * function that turns the query into a create query
   *
   * @param   array   $toCreate - holds all of the paramaters for the create call
   * @return  Migration
   */
  public function create(array $toCreate)
  {
    $this->components["action"] = "create";
    $this->components["selectors"] = $toCreate;

    return $this;
  }

  /**
   * function that turns the query into a update/alter query
   *
   * @param   array   $toAlter - field(s) that you wish to alter/update
   * @return  Migration
   */
  public function alter(array $toAlter = [])
  {
    $this->components["action"] = "update";
    if(!empty($toAlter["selectors"]) && $toAlter["values"]){
      $this->components["selectors"] = $toAlter["selectors"];
      $this->components["values"] = $toAlter["values"];
    } else if (!empty($toAlter["values"])) {
      $this->components["values"] = $toAlter["values"];
    }

    return $this;
  }

  /**
   * function that turns the query into a delete/drop query
   *
   * @param   array   $toDrop - field(s) that you wish to drop/delete
   * @return  Migration
   */
  public function drop(array $toDrop = [])
  {
    $this->components["action"] = "delete";
    $this->components["selectors"] = $toDrop;

    return $this;
  }

  /**
   * function that defines the end of the query, and adds the affected table to the query object
   *
   * @return  Migration
   */
  public function endQuery()
  {
    $this->components["tables"] = $this->tables;

    return $this;
  }
}