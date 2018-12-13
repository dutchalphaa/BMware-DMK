<?php
/**
 * @package BmBuilder
 */

namespace access;

use \config\DatabaseConfig;
use \models\DatabaseSchema;

class Database
{
  private $databaseSchema;
  private $config;
  private $conn;

  public function __construct(DatabaseConfig $config)
  {
    $this->config = $config;
    $this->conn = $config->conn;
  }

  public function useSchema()
  {

  }

}