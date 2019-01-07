<?php
/**
 * @package BMware DMK
 */

namespace config;

use \access\WordpressDatabase;

/**
 * class that takes care of setting up the database for later use
 */
class WordpressDatabaseConfig  
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

  /**
   * initialize variables
   */
  public function __construct(string $servername, string $username, string $password)
  {
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
    $config = new WordpressDatabaseConfig();
    $config->createDatabase();

    return new WordpressDatabase($config);
  }
}