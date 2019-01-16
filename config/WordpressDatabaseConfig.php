<?php
/**
 * @package BMware DMK
 */

namespace config;

use \dist\WordpressDatabase;

/**
 * class that sets up the database to use with wordpress
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
   * function that creates the database object
   *
   * @return  WordpressDatabase
   */
  public static function create()
  {
    $config = new WordpressDatabaseConfig();

    return new WordpressDatabase($config);
  }
}