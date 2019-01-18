<?php
/**
 * @package BMware DMK
 */

namespace models;

/**
 * class that holds the results for a query, and some access modifiers
 */
class DatabaseResult
{
  /**
   * variable that holds the amount of rows returned by the query
   *
   * @var int
   */
  public $numRows;
  /**
   * variable that indicates wether to use the $this->modifiedRows variable as the base of the modifier function.
   * default is false.
   *
   * @var boolean
   */
  private $useModified = false;
  /**
   * variable that stores the previous value of midifiedRows, gets set when $this->getRows("modified") is called
   *
   * @var array
   */
  private $savedModifiedRows;
  /**
   * variable that holds the current modifiedRows, gets set when $this->useModified == true and a modifier function is called
   *
   * @var array
   */
  private $modifiedRows = [];
  /**
   * variable that holds a message if no rows are returned
   *
   * @var string
   */
  private $result;
  /**
   * variable that holds the unmodified results of the query
   *
   * @var array
   */
  private $rows;

  /**
   * initialize some variables
   *
   * @param   array|string  $rows
   * @param   integer       $numRows
   */
  public function __construct($rows, int $numRows)
  {
    if(is_string($rows)){
      $this->result = $rows;
    } else {
      $this->rows = $rows;
    }
    $this->numRows = $numRows;
  }

  /**
   * sets the value of the $this->useModified variable to the value $value
   *
   * @param   boolean   $value
   * @return  void
   */
  public function setUseModified(bool $value)
  {
    $this->useModified = $value;
    if($value){
      $this->modifiedRows = $this->rows;
    } else {
      $this->modifiedRows = [];
    }
    return $this;
  }

  /**
   * returns the value of the $this->useModified variable
   *
   * @return void
   */
  public function getUseModified()
  {
    return $this->useModified;
  }

  /**
   * modifies the rows to only return the row with the given index
   *
   * @param   integer   $index
   * @return  void
   */
  public function getRowByIndex(int $index)
  {
    if($this->useModified){
      $rows = $this->modifiedRows;
      $this->modifiedRows = [];
    } else {
      $rows = $this->rows;
    }

    return $rows[$index];
  }

  /**
   * mofifies the rows to only return the rows were the value of the given $field matches the given $value
   *
   * @param   string  $field
   * @param   string  $value
   * @return  void
   */
  public function getRowsByFieldValue(string $field, string $value)
  {
    $newModifiedRows = [];

    $this->iterate(function($index, $row) use(&$field, &$value, &$newModifiedRows){
      foreach($row as $fieldName => $fieldValue){
        if($fieldName === $field && $fieldValue === $value) {
          array_push($newModifiedRows, $row);
        }
      }  
    });

    $this->modifiedRows = $newModifiedRows;

    return $this;
  }

  /**
   * modifies the rows to only return the given ...$fields
   *
   * @param string ...$fields
   * @return void
   */
  public function selectFields(string ...$fields)
  {
    $newModifiedRows = [];

    $this->iterate(function($index, $row) use(&$fields, &$newModifiedRows){
        foreach ($row as $field => $value) {
          if(in_array($field, $fields)){
            $newModifiedRows[$index][$field] = $value;
          }
        }
    });
    $this->modifiedRows = $newModifiedRows;

    return $this;
  }

  /**
   * returns either the previous mofified, modified or base rows depending on the flag,
   * flags: previous, modified. defaults to the base rows 
   *
   * @param   string  $flag
   * @return  void
   */
  public function getRows(string $flag = "")
  {
    if($flag === "modified"){
      $rows = $this->modifiedRows;
      $this->savedModifiedRows = $this->modifiedRows;
      $this->modifiedRows = $this->rows;
    } elseif($flag === "previous") {
      $rows = $this->savedModifiedRows;
    } else {
      $rows = $this->rows;
    }

    $this->variables = [];

    return $rows;
  }

  /**
   * returns the string stored in $this->result
   *
   * @return bool|null
   */
  public function getMessage()
  {
    if(isset($this->result)){
      return $this->result;
    } elseif (is_array($this->rows) && isset($this->rows)) {
      return true;
    }
    return null;
  }

  /**
   * iterates over the rows and calls a function on all of the rows, or on all of the values if $recursive = true
   *
   * @param   callable $function
   * @param   boolean $recursive
   * @return  void
   */
  public function iterate(callable $function, bool $recursive = false)
  {
    if($this->useModified){
      $rows = $this->modifiedRows;
    }else {
      $rows = $this->rows;
    }

    if(isset($this->result)){
      $function($this->result);
    } else {
      foreach($rows as $index => $row){
        if($recursive){
          foreach($row as $key => $value){
            $function($key, $value);
          }
        }else {
          $function($index, $row);
        }
      }
    }
    
    return $this; 
  }
}