<?php
/**
 * @package BmBuilder
 */

namespace access;

//use \engine\MigrationCreator;
use \models\DatabaseSchema;
use \helpers\BaseQuery;
use \helpers\IBaseQuery;

class Migration extends BaseQuery
{
  public function __construct(array $tables)
  {
    //parent::__construct();
    $this->tables = $tables;
  }

  public static function start(array $tables)
  {
    $migration = new Migration($tables);

    return $migration;
  }

  public static function excecuteQuery($conn, string $query)
  {
    $result = \mysqli_query($conn, $query);

    if(\mysqli_error($conn)){
      throw new \Exception("Query invalid, here's what went wrong: " . \mysqli_error($conn));
    }
    //cast into a database result object
    var_dump($result);
  }

  public function create(array $toCreate)
  {
    $this->components["action"] = "create";
    $this->components["selectors"] = $toCreate;

    return $this;
  }

  public function alter(array $toAlter)
  {
    $this->components["action"] = "update";
    $this->components["selectors"] = $toAlter["selectors"];
    $this->components["values"] = $toAlter["values"];

    return $this;
  }

  public function drop(string $array)
  {
    $this->components["action"] = "drop";
    $this->components["selectors"] = $toDrop; 

    return $this;
  }

  public function endQuery()
  {
    $this->components["tables"] = $this->tables;

    return $this;
  }

  public function showComponents()
  {
    echo "<pre>";print_r($this->components);echo "</pre>";

    return $this;
  }
}