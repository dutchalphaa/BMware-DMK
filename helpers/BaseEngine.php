<?php
/**
 * @package BMware DMK
 */

namespace helpers;

/**
 * base class for the query engines
 */
class BaseEngine
{
  /**
   * holds the query components
   *
   * @var   array
   */
  public $components;
  /**
   * holds the sql string
   *
   * @var   string
   */
  public $query;

  /**
   * set up initial values
   *
   * @param   array   $components
   */
  public function __construct(array $components)
  {
    $this->components = $components;
  }
}