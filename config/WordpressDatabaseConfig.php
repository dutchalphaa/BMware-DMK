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
   * holds wp connection to the database
   *
   * @var   string
   */
  public $conn;
  /**
   * holds the prefix of the website
   *
   * @var   string
   */
  public $prefix;
  
  /**
   * initialize variables
   */
  public function __construct()
  {
    global $wpdb;
    $this->conn = $wpdb;
    $this->prefix = $wpdb->prefix;
  }

  /**
   * function that creates a database object with the given information
   *
   * @return  WordpressDatabase
   */
  public static function create()
  {
    $config = new WordpressDatabaseConfig();

    return new WordpressDatabase($config);
  }
}