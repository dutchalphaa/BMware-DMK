<?php
/**
 * @package BMware DMK
 */

namespace engines;

use \helpers\QueryCreatorHelper;
use \helpers\BaseEngine;
use \dist\Query;

/**
 * class that turns the Query object into a valid MySQLi statement
 */
class QueryCreator extends BaseEngine
{
  /**
   * trait that has some functions for dealing with decoding the object to a query string
   */
  use QueryCreatorHelper;

  /**
   * function that is exposed, takes in the querycomponents from the query object, and
   * checks the action index for the action to take. then calls the apropriate method
   * per action
   *
   * @param   array   $queryComponents - array with the selectors and values for the query
   * @return  Query
   */
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
        $createQuery->remove();
        return $createQuery->query;
    }
  }


  /**
   * function that takes the query components of the query object and turns it into a sql statement,
   * adds a row to the table.
   *
   * @return  void
   */
  public function create()
  {
    $stringComponents = [];
    $comp = $this->components;

    if(!is_array($comp["tables"])){
      array_push($stringComponents, "INSERT INTO `" . $comp["tables"] . "`");
    }
    
    array_push($stringComponents, $this->enclosedValues("selectors", "`"));
    array_push($stringComponents, "VALUES " . $this->enclosedValues("values", "'"));

    $this->query = \implode(" ", $stringComponents);
  }

  /**
   * function that takes the query components of the query object and turns it into a sql statement,
   * returns the selected rows
   *
   * @return  void
   */
  public function read()
  {
    $stringComponents = [];
    $comp = $this->components;

    array_push($stringComponents, "SELECT " . $this->mutlipleValues("selectors"));

    if(!is_array($comp["tables"])){
      array_push($stringComponents, "FROM " . $comp["tables"]);
    }

    if(isset($comp["where"])){
      array_push($stringComponents, $this->whereStatements());
    }

    $this->query = \implode(" ", $stringComponents);
  }

  /**
   * function that takes the query components of the query object and turns it into a sql statement,
   * updates the selected rows
   *
   * @return  void
   */
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

    $this->query = \implode(" ", $stringComponents);
  }

  /**
   * function that takes the query components of the query object and turns it into a sql statement,
   * delete's the selected rows
   *
   * @return  void
   */
  private function remove()
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
}
