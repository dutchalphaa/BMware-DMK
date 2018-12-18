<?php
/**
 * @package BmBuilder
 */

namespace engines;

use \helpers\QueryCreatorHelper;
use \helpers\BaseEngine;
use \access\Query;

class QueryCreator extends BaseEngine
{
  use QueryCreatorHelper;

  public static function createQuery(array $queryComponents)
  {
    switch ($queryComponents["action"]) {
      case "create":
        $createQuery = new QueryCreator($queryComponents);
        $createQuery->create();
        return $createQuery->query;
      
      case "read":
        $createQuery = new QueryCreator($queryComponents);
        $createQuery->read();
        return $createQuery->query;

      case "update":
        $createQuery = new QueryCreator($queryComponents);
        $createQuery->update();
        return $createQuery->query;
      
      case "delete":
        $createQuery = new QueryCreator($queryComponents);
        $createQuery->del();
        return $createQuery->query;
    }
  }

  public function create()
  {
    $stringComponents = [];
    $comp = $this->components;

    if(!is_array($comp["tables"])){
      array_push($stringComponents, "INSERT INTO `" . $comp["tables"] . "`");
    }
    
    array_push($stringComponents, "" . $this->enclosedValues("selectors", "`"));
    array_push($stringComponents, "VALUES " . $this->enclosedValues("values", "'"));

    $this->query = \implode("", $stringComponents);
  }

  public function read()
  {
    $stringComponents = [];
    $comp = $this->components;

    array_push($stringComponents, "SELECT" . $this->mutlipleValues("selectors"));

    if(!is_array($comp["tables"])){
      array_push($stringComponents, "FROM " . $comp["tables"]);
    }

    if(isset($comp["where"])){
      array_push($stringComponents, $this->whereStatements());
    }

    $this->query = \implode(" ", $stringComponents);
  }

  private function update()
  {
    $stringComponents = [];
    $comp = $this->components;

    if(!is_array($comp["tables"])){
      array_push($stringComponents, "UPDATE " . $comp["tables"]);
    }

    array_push($stringComponents, $this->setStatements());

    if(isset($comp["where"])){
      array_push($stringComponents, $this->whereStatements());
    }

    //echo "<pre>";print_r($stringComponents);echo "</pre>";
    $this->query = \implode(" ", $stringComponents);
  }

  private function del()
  {
    $stringComponents = [];
    $comp = $this->components;

    if(!is_array($comp["tables"])){
      array_push($stringComponents, "DELETE FROM " . $comp["tables"]);
    }

    if(isset($comp["where"])){
      array_push($stringComponents, $this->whereStatements());
    }

    $this->query = \implode(" ", $stringComponents);
  }

  //conditional functions that test certain db things here
}
