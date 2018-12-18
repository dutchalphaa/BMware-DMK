<?php
/**
 * @package BmBuilder
 */

namespace helpers;

abstract class BaseQuery  
{
  public $tables;
  public $isSchema = false;
  public $components;
  
  /*public function __construct()
  {
    $this->components["action"] = "";
    $this->components["selectors"] = [];
    $this->components["values"] = [];
  }*/
}
