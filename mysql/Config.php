<?php
/**
 * @package BMware DMK
 */

namespace mysql;

use mysql\migrations\CreateMigration;
use database\MySQLi;
use database\Wordpress;

class Config  
{
  protected $wordpress;
  protected $databaseName;
  protected $serverName;
  protected $username;
  protected $password;
  protected $prefix;
  protected $conn;

  public static function create()
  {
    return new self;
  }

  public function select(bool $wordpress = false)
  {
    if($wordpress){
      $this->wordpress = true;
    } else {
      $this->wordpress = false;
    }

    return $this;
  }

  public function setCredentials(string $username = "root", string $password = "", string $serverName = "localhost")
  {
    if($this->wordpress){
      throw new \Exception("no credentials needed for wordpress configuration");
    }

    $this->username = $username;
    $this->password = $password;
    $this->serverName = $serverName;

    return $this;
  }

  public function selectDatabase(string $databaseName)
  {
    if($this->wordpress){
      throw new \Exception("wordpress automatically selects the database for you");
    }

    $this->conn = new \mysqli($this->serverName, $this->username, $this->password, $databaseName);
    $this->databaseName = $database;

    return $this;
  }

  public function createDatabase(string $databaseName = "bmbuilder_testing")
  {
    if($this->wordpress){
      throw new \Exception("No need to make a new database for wordpress configuration");
    }
    
    $this->conn = new \mysqli($this->serverName, $this->username, $this->password);
    if($this->conn->connect_error){
      throw new \Exception("failed to connect to database, heres what went wrong: " . $this->conn->connect_error);
    }

    $this->conn->query(
      CreateMigration::create($databaseName)
      ->select("database")
      ->endQuery()
      ->getQuery()
    );

    mysqli_select_db($this->conn, $databaseName);
    $this->databaseName = $databaseName;

    return $this;
  }

  public function getDatabase()
  {
    if($this->wordpress){
      global $wpdb;
      $this->conn = $wpdb;
      return new Wordpress($this);
    }
    return new MySQLi($this);
  }

  public function getConn()
  {
    return $this->conn;
  }

  public function getPrefix()
  {
    return $this->prefix;
  }

  public function getDatabaseName()
  {
    return $this->databaseName;
  }
}
