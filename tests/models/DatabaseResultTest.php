<?php

use PHPUnit\Framework\TestCase;
use models\DatabaseResult;
use config\DatabaseConfig;
use dist\Query;

class DatabaseResultTest extends TestCase
{
  protected $database;
  protected $result;

  public function setUp()
  {
    $this->database = DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
      "useExistingDatabase" => true,
      "databaseName" => "bmbuilder_testing"
    ]);
    $this->database->define(function($context){
      return $context("excecuteQuery", "CREATE TABLE IF NOT EXISTS `testing` (
        `id` INT(30) AUTO_INCREMENT,
        `string_value_one` VARCHAR(255),
        `string_value_two` VARCHAR(255),
        `int_value_one` INT(255),
        PRIMARY KEY (id)
      )");  
    });

    $this->database->define(function($context){
      return Query::start("testing")
      ->insert(["selectors" => [
        "id",
        "string_value_one", 
        "string_value_two", 
        "int_value_one"
      ], "values" => [
        "3",
        "hello",
        "world",
        "21231"
      ]])
      ->endQuery();
    });

    $this->database->define(function($context){
      return Query::start("testing")
      ->insert(["selectors" => [
        "id",
        "string_value_one", 
        "string_value_two", 
        "int_value_one"
      ], "values" => [
        "2",
        "hello",
        "there",
        "22"
      ]])
      ->endQuery();
    });

    $this->database->define(function($context){
      return Query::start("testing")
      ->insert(["selectors" => [
        "id",
        "string_value_one", 
        "string_value_two", 
        "int_value_one"
      ], "values" => [
        "1",
        "goodbye",
        "mister",
        "12314234"
      ]])
      ->endQuery();
    });

    $this->result = $this->database->define(function($context){
      return Query::start("testing")
      ->select()
      ->endQuery();
    });
  }

  /**
   * @test
   */
  public function getRows_returns_all_rows()
  {
    $this->assertInstanceOf(DatabaseResult::class, $this->result);
    $this->assertNotCount(0, $this->result->getRows());
  }

  /**
   * @test
   */
  public function getRowByIndex_returns_specified_row()
  {
    $this->assertSame($this->result->getRows()[0], $this->result->getRowByIndex(0));
    $this->assertCount(count($this->result->getRows()[0]) ,$this->result->getRowByIndex(0));
  }

  /**
   * @test
   */
  public function getRowsByFieldValue_all_result_same_field_value()
  {
    $result = $this->result;
    $result->setUseModified(true);

    $testResult = $result->getRowsByFieldValue("string_value_one", "hello")->getRows("modified");
    $this->assertCount(2, $testResult);

    $result->getRowsByFieldValue("string_value_one", "hello")->iterate(function($index, $row){
      $this->assertSame("hello", $row["string_value_one"]);
    });
  }

  /**
   * @test
   */
  public function selectFields_all_results_only_selected_fields()
  {

  }

  public function tearDown()
  {
    $this->database->define(function($context){
      $context("excecuteQuery", "DROP TABLE `testing`");
    });
  }
}
