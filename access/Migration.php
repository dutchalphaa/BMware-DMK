<?php
/**
 * @package BMware DMK
 */

namespace access;

use \models\DatabaseSchema;
use \helpers\BaseQuery;
use \helpers\IBaseQuery;

class Migration extends BaseQuery
{
  public function __construct(array $tables, DatabaseSchema $schema)
  {
    $this->tables = $tables;
    $this->schema = $schema;
  }

  public static function start(array $tables, DatabaseSchema $schema)
  {
    $migration = new Migration($tables, $schema);

    return $migration;
  }

  public function create(array $toCreate)
  {
    $this->components["action"] = "create";
    $this->components["selectors"] = $toCreate;

    return $this;
  }

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

  public function drop(array $selectors = [])
  {
    $this->components["action"] = "delete";
    $this->components["selectors"] = $selectors;

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