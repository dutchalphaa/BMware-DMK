<?php
/**
 * @package BmBuilder
 */

namespace traits;

/**
 * trait that holds function for the query class
 */
trait QueryCreatorHelper
{
  private function enclosedValues(string $comp, string $delimiter/* choose a better name for this variable */)
  {
    $enclosedValues = "(";   
    for ($i=0; $i < \count($this->components[$comp]); $i++) { 
      if($i != \count($this->components[$comp]) - 1){
        $enclosedValues .= " ". $delimiter . $this->components[$comp][$i]. $delimiter . ",";
      }else {
        $enclosedValues .= " " . $delimiter . $this->components[$comp][$i] . $delimiter;
      }
    }
    $enclosedValues .= ")";
    return $enclosedValues;
  }

  private function mutlipleValues(string $comp)
  {
    $multipleValues = "";   
    for ($i=0; $i < \count($this->components[$comp]); $i++) { 
      if($i != \count($this->components[$comp]) - 1){
        $multipleValues .= " " . $this->components[$comp][$i] . ",";
      }else {
        $multipleValues .= " " . $this->components[$comp][$i];
      }
    }
    return $multipleValues;
  }

  private function whereStatements()
  {
    $count = 1;
    $whereStatements = "WHERE ";
    foreach($this->components["where"] as $key => $value){
      if($count != \count($this->components["where"])){
        $whereStatements .= "`" . $key . "` = '" . $value . "' AND ";
        $count++;
      }else {
        $whereStatements .= "`" . $key . "` = '" . $value . "'";
      }
    }

    return $whereStatements;
  }

  private function setStatements()
  {
    $setStatements = "SET ";   
    for ($i=0; $i < \count($this->components["selectors"]); $i++) { 
      if($i != \count($this->components["selectors"]) - 1){
        $setStatements .= "`" . $this->components["selectors"][$i] . "` = '" . $this->components["values"][$i] . "' AND ";
      }else {
        $setStatements .= "`" . $this->components["selectors"][$i] . "` = '" . $this->components["values"][$i] . "'";
      }
    }
    return $setStatements;
  }
}
