<?php
/**
 * @package BMware DMK
 */

namespace database;

use config\WordpressDatabaseConfig;
use models\DatabaseResult;
use helpers\BaseDatabase;

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
  private function excecuteQuery(string $query)
  {
    $result = $this->conn->get_results($query, ARRAY_A);
    
    if(empty($result)){
      $result = "query excecuted, result unknown";
      $count = 0;
    } else {
      $count = count($result);
    }


    return new DatabaseResult($result, $count);
  }
}