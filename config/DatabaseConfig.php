<?php
/**
 * @package BMware DMK
 */

namespace config;

use \access\Database;

class DatabaseConfig  
{
  public $databaseName = "bmbuilder_database";
  private $createFunction = false;
  public $servername;
  public $username;
  public $password;
  public $conn;

  public function __construct($servername, $username, $password)
  {
    $this->servername = $servername;
    $this->username = $username;
    $this->password = $password;
  }

  public function createDatabase()
  {
    $this->connect(true);
    if (\mysqli_query($this->conn, "CREATE DATABASE IF NOT EXISTS $this->databaseName") != true) {
        throw new \Exception("Error creating database: " . \mysqli_error($this->conn));
    } 
    \mysqli_close($this->conn);
    $this->connect();
  }

  public function connect(bool $createDB = false)
  { 
    if($this->createFunction != true){
      throw new \Exception("this class will only work if you call it from the static create method");
    }

    if($createDB){
      $this->conn = \mysqli_connect($this->servername, $this->username, $this->password); 
      if (!$this->conn) {
          throw new \Exception("Connection failed: " . mysqli_connect_error());
      }
    }else {
      $this->conn = \mysqli_connect($this->servername, $this->username, $this->password, $this->databaseName); 
      if (!$this->conn) {
          throw new \Exception("Connection failed: " . mysqli_connect_error());
      }
    }
  }

  public static function create(array $conectionVariables)
  {
    $config = new DatabaseConfig($conectionVariables["servername"], $conectionVariables["username"], $conectionVariables["password"]);
    $config->createFunction = true;

    if(isset($conectionVariables["useExistingDatabase"]) && $conectionVariables["useExistingDatabase"] == true && isset($conectionVariables["databaseName"])){
      $config->databaseName = $conectionVariables["databaseName"];
      $config->connect();
    }else {
      if(isset($conectionVariables["databaseName"]))
      {
        $config->databaseName = $conectionVariables["databaseName"];
      }
      

      $config->createDatabase();
    }

    return new Database($config);
  }
}