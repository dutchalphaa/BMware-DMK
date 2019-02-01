<?php
/**
 * @package BMware DMK
 */

namespace mysql\migrations;

use helpers\BaseCrudMigration;

class DeleteMigration extends BaseCrudMigration
{
  public function select(string $flag = "table")
  {
    switch ($flag) {
      case "table":
        $this->components["selectors"] = "DROP TABLE $this->table";
        break;

      case "database":
        $this->components["selectors"] = "DROP DATABASE $this->table";
        break;

      case "column":
        $this->components["selectors"] = "ALTER TABLE $this->table";
        break;

      default:
        throw new \Exception("the flag variable expects the values of table, database or column, got $flag");
        break;
    }
    $this->flag = $flag;
    return $this;
  }

  public function dropColumn(string $name)
  {
    if($this->flag !== "column"){
      throw new \Exception("can't drop column if the selected query isn't column");
    }

    if(!isset($this->components["column"])){
      $this->components["column"] = [];
    }

    $this->encloseBackticks($name);

    array_push($this->components["column"], "DROP COLUMN $name");
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