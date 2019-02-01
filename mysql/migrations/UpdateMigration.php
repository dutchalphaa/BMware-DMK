<?php
/**
 * @package BMware DMK
 */

namespace mysql\migrations;

use helpers\BaseCrudMigration;

class UpdateMigration extends BaseCrudMigration
{  
  public function select(string $flag = "table")
  {
    switch ($flag) {
      case "table":
      case "column":
        $this->components["selectors"] = "ALTER TABLE $this->table";
        break;

      default:
        throw new \exceptions\InvalidArgumentException("the flag variable expects the values of table or column, got $flag");
        break;
    }

    $this->flag = $flag;
    return $this;
  }

  public function changeTableName(string $name)
  {
    if($this->flag !== "table"){
      throw new \Exception("can't rename a table if the selected query isn't table");
    }

    $this->encloseBackticks($name);

    $this->components["table"] = "RENAME $name";

    $this->table = $name;
    return $this;
  }

  public function alterColumn(string $columnName, string $dataType, int $length = 255, string $newName = "", bool $ai = false, bool $notNull = false, bool $primary = false)
  {
    if($this->flag !== "column"){
      throw new \Exception("can't alter a table if the selected query isn't column", 1);
    }

    $this->encloseBackticks($columnName);

    if(!isset($this->components["column"])){
      $this->components["column"] = [];
    }

    if($newName !== ""){
      $this->encloseBackticks($newName);
      $string = "CHANGE";
    } else {
      $string = "MODIFY";
    }
    $string .= " $columnName ";
    if($newName !== ""){
      $string .= "$newName ";
    }

    $string .= strtoupper($dataType) . "($length)";
    if($notNull){
      $string  .= " NOT NULL";
    }
    if($ai){
      $string .= " AUTO_INCREMENT PRIMARY KEY";
    }else if($primary){
      $string .= " PRIMARY KEY";
    }    

    array_push($this->components["column"], $string);
    return $this;
  }

  public function endQuery()
  {
    if(isset($this->components["column"])){
      $this->components["column"] = implode(", ", $this->components["column"]);
    }

    $this->query = implode(" ", $this->components);

    static::$lastQuery = $this;
    return $this;
  }
}