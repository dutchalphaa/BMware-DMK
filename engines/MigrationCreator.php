<?php
/**
 * @package BmBuilder
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
      case 'create':
        $createMigration = new MigrationCreator($queryComponents);
        $createMigration->create();
        return $createMigration->query;
      
      default:
        # code...
        break;
    }
  }
  
  //create backup takes in the previous schema, queries all known tables for their data.

  //create table
  public function create()
  {
    $stringComponents = [];
    $comp = $this->components;

    var_dump($this->components);
    die();
  }

  //update table

  //delete table
}