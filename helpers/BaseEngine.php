<?php
/**
 * @package BmBuilder
 */

namespace helpers;

class BaseEngine
{
  public $components;
  public $query;

  public function __construct(array $components)
  {
    $this->components = $components;
  }
}