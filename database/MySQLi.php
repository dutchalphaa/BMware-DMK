<?php
/**
 * @package BMware DMK
 */

namespace database;

use models\DatabaseResult;
use mysql\Config;
use helpers\BaseCrudSQL;
use helpers\BaseDatabase;

/**
 * class that has all of the functionality for making calls to the SQL database
 */
final class MySQLi extends BaseDatabase
{
  /**
   * initialize some variable for the database object
   *
   * @param   Config  $config - holds all the config options for the database object
   */
  public function __construct(Config $config)
  {
    $this->conn = $config->getConn();
    $this->databaseName = $config->getDatabaseName();
  }

  /**
   * Function that excecutes the function on the database, and casts the result into a DatabaseResult object.
   * Can be called manualy however this is advised against.
   *
   * @param string $query
   * @return DatabaseResult
   */
  protected function executeQuery($query)
  {
    //checks if the query extends BaseCrudQuery class and has the variables for prepared statements set
    if(is_subclass_of($query, BaseCrudSQL::class) && count($query->getVariables()) > 0) {
      $statement = $this->conn->prepare($query->getQuery());
      $statement->bind_param($query->getPreparedTypes(), ...$query->getVariables());
  
      
      if(!$statement->execute()){
        throw new \exceptions\InvalidQueryException("Query invalid, here's what went wrong: " . mysqli_stmt_get_warnings($statement));
      }
  
      $result = mysqli_stmt_get_result($statement);
      $statement->close();
    } else if(is_subclass_of($query, BaseCrudSQL::class) || is_string($query)) {
      if(is_subclass_of($query, BaseCrudSQL::class)){
        $query = $query->getQuery();
      }

      $result = mysqli_query($this->conn, $query);

      if(mysqli_error($this->conn)){
        throw new \exceptions\InvalidQueryException("Query invalid, here's what went wrong: " . mysqli_error($this->conn));
      }

    } else {
      throw new \InvalidArgumentException("This function expexts a string or one of the Query objects");
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