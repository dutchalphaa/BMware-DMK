<?php

use PHPUnit\Framework\TestCase;
use models\DatabaseResult;
use config\DatabaseConfig;
use queries\ReadQuery;

class MySQLiTest extends TestCase
{
  protected $database;
  protected $databaseName = "bmbuilder_testing";

  public function setUp()
  {
    $this->database = DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
      "useExistingDatabase" => true,
      "databaseName" => "$this->databaseName"
    ]);
  }

  /**
   * @test
   */
  public function access_forbidden_argument_in_define_with_context_function()
  {
    $this->expectException(\exceptions\InvalidContextArgumentException::class);
    $this->database->define(function($context){
      $context("conn");
    });
  }

  /**
   * @test
   */
  public function try_get_access_at_forbidden_timing()
  {
    $this->expectException(\exceptions\NoAccessException::class);
    $this->database->getAccess("conn");
  }

  /**
   * @test
   */
  public function database_returns_database_result_class()
  {
    $result = $this->database->define(function($context){
      return ReadQuery::create("user")
      ->select()
      ->endQuery();
    });
    
    $this->assertInstanceOf(DatabaseResult::class, $result);
    $this->assertGreaterThan(0, $result->numRows);
    $this->assertNotCount(0, $result->getRows());
  }

  /**
   * @test
   */
  public function excecute_query_function_returns_database_result_class()
  {
    $result = $this->database->define(function($context){
      return $context("excecuteQuery", "SELECT * FROM `user`");
    });

    $this->assertInstanceOf(DatabaseResult::class, $result);
    $this->assertGreaterThan(0, $result->numRows);
    $this->assertNotCount(0, $result->getRows());
  }

  /**
   * @test
   */
  public function invalid_query_throws_invalid_query_exception()
  {
    $this->expectException(\exceptions\InvalidQueryException::class);
    $this->database->define(function($context){
      $context("excecuteQuery","hello world");
    });
  }

  /**
   * @test
   */
  public function invalid_return_type_throws_invalid_return_type_exception()
  {
    $this->expectException(\exceptions\InvalidDefineReturnType::class);
    $this->database->define(function($context){
      return true;
    });
  }
}