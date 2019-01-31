<?php
/**
 * @package BMware DMK
 */

namespace helpers;

abstract class BaseCrudMigration
{
  use CrudSQLHelper;

  protected $flag;

  abstract public function endQuery();
  abstract public function select(string $flag = "table");
}