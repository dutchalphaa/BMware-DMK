<?php
/**
 * @package BMware DMK
 */

namespace database;

use models\DatabaseResult;
use config\DatabaseConfig;
use helpers\BaseDatabase;

/**
 * class that has all of the functionality for making calls to the SQL database
 */
final class MySQLi extends BaseDatabase
{
  /**
   * holds the name of the database
   *
   * @var string
   */
  protected $databaseName;

  /**
   * initialize some variable for the database object
   *
   * @param   DatabaseConfig  $config - holds all the config options for the database object
   */
  public function __construct(DatabaseConfig $config)
  {
    $this->conn = $config->conn;
    $this->databaseName = $config->databaseName;
  }

  /**
   * Function that excecutes the function on the database, and casts the result into a DatabaseResult object.
   * Can be called manualy however this is advised against.
   *
   * @param string $query
   * @return DatabaseResult
   */
  protected function excecuteQuery(string $query)
  {
    $result = \mysqli_query($this->conn, $query);

    if(\mysqli_error($this->conn)){
      throw new \exceptions\InvalidQueryException("Query invalid, here's what went wrong: " . \mysqli_error($this->conn));
    }
    $queryResult = '';
    $numrows = 0;

    //cast into a database result object
    if(is_bool($result)){
      if($result) {
        $queryResult = "row(s) successfully added/altered";
      }else{
        $queryResult = "row(s) not added/altered";
      }
    }else {
      $rows = [];
  
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          array_push($rows, $row);
        }

        $queryResult = $rows;
        $numrows = $result->num_rows;

      } else {
        $queryResult = "0 results";
      }
    }
    return new DatabaseResult($queryResult, $numrows);
  }
}