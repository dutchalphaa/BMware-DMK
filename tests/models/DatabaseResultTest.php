<?php

use PHPUnit\Framework\TestCase;
use models\DatabaseResult;
use config\DatabaseConfig;
use queries\ReadQuery;
use queries\CreateQuery;

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
      return CreateQuery::create("testing")
      ->select("id", "string_value_one", "string_value_two", "int_value_one")
      ->values("3", "hello", "world", "21231")
      ->values("2", "hello", "there","22")
      ->values("1", "goodbye", "mister", "12314234")
      ->endQuery();
    });

    $this->result = $this->database->define(function($context){
      return ReadQuery::create("testing")
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
    $result = $this->result;
    $result->setUseModified(true);

    $testResult = $result->selectFields("int_value_one")->getRows("modified");
    $this->assertCount(3, $testResult);

    $result->selectFields("int_value_one")->iterate(function($index, $row){
      $this->assertArrayNotHasKey("string_value_one", $row);
      $this->assertArrayNotHasKey("string_value_two", $row);
      $this->assertArrayNotHasKey("id", $row);
      $this->assertCount(1, $row);
    });
  }

  /**
   * @test
   */
  public function selectFields_and_getRowsByFieldValue_work_toghetter()
  {
    $result = $this->result;
    $result->setUseModified(true);

    $testResult = $result->getRowsByFieldValue("id", "1")->selectFields("id")->getRows("modified");
    $this->assertCount(1, $testResult);

    $result->getRowsByFieldValue("id", "1")->selectFields("id")->iterate(function($index, $row){
      $this->assertArrayNotHasKey("string_value_one", $row);
      $this->assertArrayNotHasKey("int_value_one", $row);
      $this->assertArrayNotHasKey("string_value_two", $row);
      $this->assertCount(1, $row);
    });
  }

  /**
   * @test
   */
  public function selectFields_and_getRowsByFieldValue_work_toghetter_reversed()
  {
    $result = $this->result;
    $result->setUseModified(true);

    $testResult = $result->selectFields("id")->getRowsByFieldValue("id", "1")->getRows("modified");
    $this->assertCount(1, $testResult);

    $result->selectFields("id")->getRowsByFieldValue("id", "1")->iterate(function($index, $row){
      $this->assertArrayNotHasKey("string_value_one", $row);
      $this->assertArrayNotHasKey("int_value_one", $row);
      $this->assertArrayNotHasKey("string_value_two", $row);
      $this->assertCount(1, $row);
    });
  }

  /**
   * @test
   */
  public function iterate_doesnt_break_method_chain()
  {
    $result = $this->result;
    $result->setUseModified(true);

    $testResult = $result->selectFields("id")->getRowsByFieldValue("id", "1")->iterate(function($index, $row){})->getRows("modified");
    $this->assertCount(1, $testResult);
  }

  public function tearDown()
  {
    $this->database->define(function($context){
      $context("excecuteQuery", "DROP TABLE `testing`");
    });
  }
}
