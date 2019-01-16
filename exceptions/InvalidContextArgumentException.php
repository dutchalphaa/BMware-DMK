<?php
/**
 * @package BMware DMK
 */

namespace exceptions;

class InvalidContextArgumentException extends \Exception
{
  public $message = "This function or argument isn't accessible";
}