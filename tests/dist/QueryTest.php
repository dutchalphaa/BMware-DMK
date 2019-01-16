<?php

use PHPUnit\Framework\TestCase;
use engines\QueryCreator;
use dist\Query;

class QueryTest extends TestCase
{
  /**
   * @test
   */
  public function select_all_from_user_query_and_query_engine_test()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->select()
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("SELECT * FROM `user`", $query);
  }

  /**
   * @test
   */
  public function select_all_from_user_where_ID_equals_one()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->select()
      ->where(["ID" => "1"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("SELECT * FROM `user` WHERE `ID` = '1'", $query);
  }

  /**
   * @test
   */
  public function select_ID_from_user()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->select(["ID"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("SELECT `ID` FROM `user`", $query);
  }

  /**
   * @test
   */
  public function select_ID_created_at_from_user()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->select(["ID", "created_at"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("SELECT `ID`, `created_at` FROM `user`", $query);
  }

  /**
   * @test
   */
  public function select_ID_from_user_where_ID_equals_one()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->select(["ID"])
      ->where(["ID" => "1"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("SELECT `ID` FROM `user` WHERE `ID` = '1'", $query);
  }

  /**
   * @test
   */
  public function select_ID_created_at_from_user_where_ID_equals_one()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->select(["ID", "created_at"])
      ->where(["ID" => "1"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("SELECT `ID`, `created_at` FROM `user` WHERE `ID` = '1'", $query);
  }

  /**
   * @test
   */
  public function insert_into_user_multiple_fields()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->insert(["selectors" => ["email", "loc"], "values" => ["hello", "there"]])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("INSERT INTO `user` ( `email`, `loc` ) VALUES ( 'hello', 'there' )", $query);
  }

  /**
   * @test
   */
  public function insert_into_user_one_field()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->insert(["selectors" => ["email"], "values" => ["there"]])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("INSERT INTO `user` ( `email` ) VALUES ( 'there' )", $query);
  }

  /**
   * @test 
   */
  public function delete_from_user_where_ID_equals_one()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->remove()
      ->where(["ID" => "1"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("DELETE FROM `user` WHERE `ID` = '1'", $query);
  }

  /**
   * @test
   */
  public function delete_from_user_where_ID_equals_one_and_loc_equals_hello()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->remove()
      ->where(["ID" => "1", "loc" => "hello"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("DELETE FROM `user` WHERE `ID` = '1' AND `loc` = 'hello'", $query);
  }

  /**
   * @test
   */
  public function update_loc_to_hello_where_ID_equals_one()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->update(["selectors" => ["loc"], "values" => ["hello"]])
      ->where(["ID" => "1"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("UPDATE `user` SET `loc` = 'hello' WHERE `ID` = '1'", $query);
  }

  /**
   * @test
   */
  public function update_loc_to_hello_where_ID_equals_one_and_loc_equals_hello()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->update(["selectors" => ["loc"], "values" => ["hello"]])
      ->where(["ID" => "1", "loc" => "hello"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("UPDATE `user` SET `loc` = 'hello' WHERE `ID` = '1' AND `loc` = 'hello'", $query);
  }

  /**
   * @test
   */
  public function update_loc_to_hello_update_email_to_there_where_ID_equals_one()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->update(["selectors" => ["loc", "email"], "values" => ["hello", "there"]])
      ->where(["ID" => "1"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("UPDATE `user` SET `loc` = 'hello', `email` = 'there' WHERE `ID` = '1'", $query);
  }

  /**
   * @test
   */
  public function update_loc_to_hello_update_email_to_there_where_ID_equals_one_and_email_equals_hello()
  {
    $query = QueryCreator::createQuery(
      Query::start("user")
      ->update(["selectors" => ["loc", "email"], "values" => ["hello", "there"]])
      ->where(["ID" => "1", "email" => "hello"])
      ->endQuery()
      ->components
    );

    $this->assertInternalType("string", $query);
    $this->assertSame("UPDATE `user` SET `loc` = 'hello', `email` = 'there' WHERE `ID` = '1' AND `email` = 'hello'", $query);
  }
}