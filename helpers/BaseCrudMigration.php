<?php
/**
 * @package BMware DMK
 */

namespace helpers;

abstract class BaseCrudMigration extends BaseCrudSQL
{
  protected $flag;

  abstract public function endQuery();
  abstract public function select(string $flag = "table");
}