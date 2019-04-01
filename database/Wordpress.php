<?php
/**
 * @package BMware DMK
 */

namespace database;

use config\WordpressDatabaseConfig;
use models\DatabaseResult;
use helpers\BaseDatabase;
use helpers\BaseCrudQuery;

/**
 * class that uses the wordpress wpdb class to make calls to the database
 */
final class Wordpress extends BaseDatabase
{
  /**
   * variable that holds the prefix that is chosen by the user
   *
   * @var string
   */
  public $prefix;

  /**
   * initialize some variable for the database object
   *
   * @param   WordpressDatabaseConfig  $config - holds all the config options for the database object
   */
  public function __construct(WordpressDatabaseConfig $config)
  {
    $this->conn = $config->conn;
    $this->prefix = $config->prefix;
  }

  /**
   * function that allows you to wrtie pure SQL to the database, use only if truly necisairy.
   * because this will skip some of the optimization
   *
   * @param string $query
   * @return DatabaseResult
   */
  protected function executeQuery($query)
  {
    if(is_subclass_of($query, BaseCrudQuery::class) && count($query->getVariables()) > 0){
      $count = 1;
      $preparedTypes = str_split($query->getPreparedTypes());
      $queryString = $query->getQuery();
  
      foreach ($preparedTypes as $char) {
        if($char == "i"){
          $char = str_replace("i", "%d", $char);
        } else if ($char == "s") {
          $char = str_replace("s", "%s", $char);
        } else if ($char == "d") {
          $char = str_replace("d", "%f", $char);
        }
  
        $pos = strpos($queryString, "?");
        if ($pos !== false) {
          $queryString = substr_replace($queryString, $char, $pos, 1);
        }
      }
  
      $result = $this->conn->get_results($this->conn->prepare(
        $queryString,
        ...$query->getVariables()
      ), ARRAY_A);
    } else {
      if(is_subclass_of($query, BaseCrudQuery::class)){
        $result = $this->conn->get_results($query->getQuery(), ARRAY_A);
      } else if(is_string($query)) {
        $result = $this->conn->get_results($query, ARRAY_A);
      } else {
        throw new \Exception();
      }
    }
    
    if(empty($result)){
      $result = "query excecuted, result unknown";
      $count = 0;
    } else {
      $count = count($result);
    }

    return new DatabaseResult($result, $count);
  }
}