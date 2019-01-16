<?php
/**
 * @package BMware DMK
 */

namespace helpers;

/**
 * trait that holds function for the creation of the sql string
 */
trait QueryCreatorHelper
{
  private function enclosedValues(string $comp, string $delimiter, bool $enclosed = true)
  {
    if($enclosed){
      $enclosedValues = "( ";
    } else {
      $enclosedValues = "";
    }
    for ($i=0; $i < \count($this->components[$comp]); $i++) { 
      if($i != \count($this->components[$comp]) - 1){
        $enclosedValues .= $delimiter . $this->components[$comp][$i]. $delimiter . ", ";
      }else {
        $enclosedValues .= $delimiter . $this->components[$comp][$i] . $delimiter;
      }
    }
    if($enclosed){
      $enclosedValues .= " )";
    }
    return $enclosedValues;
  }

  private function mutlipleValues(string $comp, bool $drop = false)
  {
    $delimiter = "";
    if($drop){
      $delimiter = "DROP ";
    }
    
    $multipleValues = "";   
    for ($i=0; $i < \count($this->components[$comp]); $i++) { 
      if($i != \count($this->components[$comp]) - 1){
        $multipleValues .= "$delimiter" . $this->components[$comp][$i] . ", ";
      }else {
        $multipleValues .= "$delimiter" . $this->components[$comp][$i];
      }
    }
    return $multipleValues;
  }

  private function createTableSelectors(string $comp, bool $enclosed = false)
  {
    $multipleValues = "(";
    foreach ($this->components[$comp] as $key => $value) {
      
      if($key == "PRIMARY"){
        $multipleValues .= "PRIMARY KEY ($value)";
      } else if ($key == key(array_slice($this->components[$comp], -1, 1, true))) {
        throw new \Exception("no primary key was given");
      } else {
        $multipleValues .= "`". $key . "` " . $value . ", ";
      }
      
    }
    $multipleValues .= ")";
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
        $setStatements .= "`" . $this->components["selectors"][$i] . "` = '" . $this->components["values"][$i] . "', ";
      }else {
        $setStatements .= "`" . $this->components["selectors"][$i] . "` = '" . $this->components["values"][$i] . "'";
      }
    }
    return $setStatements;
  }
}
