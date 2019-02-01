<?php
/**
 * @package BMware DMK
 */

namespace mysql\migrations;

use helpers\BaseCrudMigration;

class CreateMigration extends BaseCrudMigration
{
  public function select(string $flag = "table")
  {
    switch ($flag) {
      case "table":
        $this->components["selectors"] = "CREATE TABLE IF NOT EXISTS $this->table";
        break;
      
      case "database":
        $this->components["selectors"] = "CREATE DATABASE IF NOT EXISTS $this->table";
        break;

      case "column":
        $this->components["selectors"] = "ALTER TABLE $this->table";
        break;

      default:
        throw new InvalidArgumentException("the flag variable expects the values of either: table, database or column. Got $flag");
        break;
    }
    $this->flag = $flag;

    return $this;
  }

  public function addRow(string $name, string $dataType, int $length = 255, bool $ai = false, bool $notNul = false, bool $primary = false)
  {
    if(!isset($this->components["columns"])){
      $this->components["columns"] = [];
    }

    if($this->flag === "column"){
      $string = "ADD ";
    } else if($this->flag === "table") {
      $string = "";
    }

    $this->encloseBackticks($name);

    $string .= "$name ";
    $string .= strtoupper($dataType) . "($length)";
    if($notNul){
      $string  .= " NOT NULL";
    }
    if($ai){
      $string .= " AUTO_INCREMENT PRIMARY KEY";
    }else if($primary){
      $string .= " PRIMARY KEY";
    }
    
    array_push($this->components["columns"], $string);

    return $this;
  }

  public function endQuery()
  {
    if(isset($this->components["columns"])){
      $this->components["columns"] = implode(", ", $this->components["columns"]);
      if($this->flag === "table"){
        $this->components["columns"] = "( " . $this->components["columns"] . " )";
      }
    }

    $this->query = implode(" ", $this->components);

    static::$lastQuery = $this;
    return $this;
  }
}