<?php
/**
 * @package BMware DMK
 */

namespace engines;

use \helpers\QueryCreatorHelper;
use \helpers\BaseEngine;

class MigrationCreator extends BaseEngine
{
  use QueryCreatorHelper;

  public static function createQuery(array $queryComponents)
  {
    switch ($queryComponents["action"]) {
      case "create":
        $createMigration = new MigrationCreator($queryComponents);
        $createMigration->create();
        return $createMigration->query;
      
      case "update":
        $createMigration = new MigrationCreator($queryComponents);
        $createMigration->update();
        return $createMigration->query;

      case "delete":
        $createMigration = new MigrationCreator($queryComponents);
        $createMigration->remove();
        return $createMigration->query;
    }
  }
  
  //create backup takes in the previous schema, queries all known tables for their data.

  //create table
  public function create()
  {
    $stringComponents = [];
    $comp = $this->components;

    if(\count($comp["tables"]) != 1){
      throw new \Exception("this query can only affect 1 table");
    }
    //if(!schema cointains the table) {
      array_push($stringComponents, "CREATE TABLE " . $comp["tables"][0]);
      
      if(isset($comp["selectors"])){
        array_push($stringComponents, $this->createTableSelectors("selectors", true));
      } 
    //} else {
      //excecute alter on table. create fields.  
    //}
    $this->query = \implode(" ", $stringComponents);
    //update the schema
  }

  //update table
  public function update()
  {
    $stringComponents = [];
    $comp = $this->components;

    if(\count($comp["tables"]) != 1){
      throw new \Exception("this query can only affect 1 table");
    }
    //if(schema contains the table && contains the field){
      if(empty($comp["selectors"])){
        array_push($stringComponents, "ALTER TABLE " . $comp["tables"][0], "RENAME TO " . $this->components["values"]);
      } else {
        array_push($stringComponents, "ALTER TABLE " . $comp["tables"][0]);
        if(is_array($comp["values"])){
          //really complicated logic here
          if(\count($comp["values"]) > 1) {
            throw new \Exception("this query can only affect 1 table, please reference the docs to see how to call multiple queries");
          }
          foreach($comp["values"] as $name => $value) {
            array_push($stringComponents, "CHANGE `" . $comp["selectors"] . "` `" . $name . "` " . $value);
          }
        } else {
          array_push($stringComponents, "MODIFY " . $comp["selectors"] . " " . $comp["values"]);
        }
      }
    //} else {
      //throw new \Exception("Field or table does not exist in the database. make sure that the fields you try to update exist in the schema")
    //}
    //echo "<pre>";print_r($stringComponents);echo "</pre>";
    $this->query = \implode(" ", $stringComponents);
    //update the schema
  }

  //delete table
  public function remove()
  {
    $stringComponents = [];
    $comp = $this->components;

    //if(schema cointains the table && table is not last) {
      if(empty($comp["selectors"])) {
        array_push($stringComponents, "DROP TABLE ");
        array_push($stringComponents, $this->mutlipleValues("tables"));
      } else {
        if(\count($comp["tables"]) != 1){
          throw new \Exception("this query can only affect 1 table");
        }
        array_push($stringComponents, "ALTER TABLE " . $comp["tables"][0], $this->mutlipleValues("selectors", true));
      }
    //}
    $this->query = \implode(" ", $stringComponents);
    //update the schema
  }
}