<?php
/**
 * @package BMware DMK
 */

namespace helpers;

/**
 * base class for the query objects
 */
class BaseQuery  
{
  /**
   * variable that holds the databaseschema
   *
   * @var DatabaseSchema
   */
  public $schema;
  /**
   * variable that holds all of the components for the query to be written
   *
   * @var array
   */
  public $components;
}
