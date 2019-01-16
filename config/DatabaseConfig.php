<?php
/**
 * @package BMware DMK
 */

namespace config;

use \dist\Database;
use \exceptions\InvalidDatabaseNameException;

/**
 * class that takes care of setting up the database for later use
 */
class DatabaseConfig  
{
  /**
   * holds the name of the database
   *
   * @var string
   */
  public $databaseName = "bmbuilder_database";
  /**
   * holds the servername of the database
   *
   * @var string
   */
  public $servername;
  /**
   * holds the username of the database
   *
   * @var string
   */
  public $username;
  /**
   * holds the password of the database
   *
   * @var string
   */
  public $password;
  /**
   * holds the connection variable to the database
   *
   * @var mysqli
   */
  public $conn;

  /**
   * initialize variables
   *
   * @param   string  $servername - the database servername
   * @param   string  $username - the database username
   * @param   string  $password - the database password
   */
  public function __construct(string $servername, string $username, string $password)
  {
    $this->servername = $servername;
    $this->username = $username;
    $this->password = $password;
  }

  /**
   * function that creates the database if one doesn't yet exist
   *
   * @return void
   */
  public function createDatabase()
  {
    $this->connect(true);
    if (\mysqli_query($this->conn, "CREATE DATABASE IF NOT EXISTS $this->databaseName") != true) {
        throw new \Exception("Error creating database: " . \mysqli_error($this->conn));
    } 
    \mysqli_close($this->conn);
    $this->connect();
  }

  /**
   * function that opens the connection to the database
   *
   * @param   boolean   $createDB - boolean value that indicates if a new database has to be made, if so
   * doesn't select a databsae to connect to.
   * @return  void
   */
  public function connect(bool $createDB = false)
  { 
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

  /**
   * function that creates a database object with the given information
   *
   * @param   array   $conectionVariables - array of values that include the database name, username, password, host
   * and variables to indicate wether to make a new database or not.
   * @return  Database
   */
  public static function create(array $conectionVariables)
  {
    $config = new DatabaseConfig($conectionVariables["servername"], $conectionVariables["username"], $conectionVariables["password"]);

    if(isset($conectionVariables["useExistingDatabase"]) && $conectionVariables["useExistingDatabase"] == true && isset($conectionVariables["databaseName"])){
      if($conectionVariables["databaseName"] === ""){
        throw new InvalidDatabaseNameException("");
      }
      $config->databaseName = $conectionVariables["databaseName"];
      $config->connect();
    }else {
      if(isset($conectionVariables["databaseName"]))
      {
        if($conectionVariables["databaseName"] === ""){
          throw new InvalidDatabaseNameException();
        }
        $config->databaseName = $conectionVariables["databaseName"];
      }
      

      $config->createDatabase();
    }

    return new Database($config);
  }
}