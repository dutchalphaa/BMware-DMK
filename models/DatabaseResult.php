<?php
/**
 * @package BMware DMK
 */

namespace models;

/**
 * class that holds the results for a query, and some access modifiers
 */
class DatabaseResult
{
  public $numRows;
  public $rows;
  public $modifiers;

  public function __construct()
  {

  }
}