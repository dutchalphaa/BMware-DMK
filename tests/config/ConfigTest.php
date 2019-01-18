<?php

use PHPUnit\Framework\TestCase;
use config\DatabaseConfig;
use dist\Query;

class ConfigTest extends TestCase
{
  /**
   * @test
   */
  public function database_class_is_returned()
  {
    $databaseName = "bmbuilder_testing";
    
    $database = DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
      "useExistingDatabase" => true,
      "databaseName" => "$databaseName"
    ]);
    
    $databaseNameOfInstance;
    $database->define(function($context) use(&$databaseNameOfInstance){
      $databaseNameOfInstance = $context("databaseName");
    });

    $this->assertInstanceOf(\database\MySQLi::class, $database);
    $this->assertEquals($databaseName, $databaseNameOfInstance);
  }

  /**
   * @test
   */
  public function invalid_database_name_exception_on_no_database_name()
  {
    $this->expectException(\exceptions\InvalidDatabaseNameException::class);
    DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
      "useExistingDatabase" => true,
      "databaseName" => ""
      ]);
    }

  /**
   * @test
   */
  public function database_class_is_still_returned_when_no_database_name_is_given_and_deafualt_is_made()
  {
    $database = DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
    ]);

    $databaseNameOfInstance;
    $database->define(function($context) use(&$databaseNameOfInstance){
      $databaseNameOfInstance = $context("databaseName");
    });

    $this->assertInstanceOf(\database\MySQLi::class, $database);
    $this->assertEquals("bmbuilder_database", $databaseNameOfInstance);
  }

  /**
   * @test
   */
  public function database_class_is_still_returned_when_database_name_is_given_and_database_is_made()
  {
    $databaseName = "bmbuilder_newdb_test";
    $database = DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
      "databaseName" => $databaseName
    ]);

    $databaseNameOfInstance;
    $database->define(function($context) use(&$databaseNameOfInstance){
      $databaseNameOfInstance = $context("databaseName");
    });

    $this->assertInstanceOf(\database\MySQLi::class, $database);
    $this->assertEquals($databaseName, $databaseNameOfInstance);
  }

  /**
   * @test
   * @expectedException \exceptions\InvalidDatabaseNameException
   */
  public function invalid_database_name_exception_on_no_database_name_when_created()
  {
    DatabaseConfig::create([
      "servername" => "localhost",
      "username" => "root",
      "password" => "",
      "databaseName" => ""
    ]);
  }
}