<?php
/**
 * @package BMware DMK
 */

namespace exceptions;

class InvalidDefineReturnType extends \Exception
{
  public $message = "Invalid return type, object returned is not of type: Query, Migration, Database or Null";
}