<?php
/**
 * @package BMware DMK
 */

namespace exceptions;

class InvalidQueryException extends \Exception
{
  public $message = "Query is invalid";
}