<?php
/**
 * @package BMware DMK
 */

namespace exceptions;

class NoAccessException extends \Exception
{
  public $message = "You do not have access to this function or property at this time";
}