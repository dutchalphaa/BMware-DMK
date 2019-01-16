<?php
/**
 * @package BMware DMK
 */

namespace exceptions;

class InvalidDatabaseNameException extends \Exception
{
  public $message = "invalid database name";
}