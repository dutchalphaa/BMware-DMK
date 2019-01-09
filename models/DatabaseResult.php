<?php
/**
 * @package BMware DMK
 */

namespace models;

use helpers\ArrayHelper;

/**
 * class that holds the results for a query, and some access modifiers
 */
class DatabaseResult
{
  use ArrayHelper;

  public $numRows;
  private $variables = [];
  private $modifiedRows;
  private $result;
  private $rows;

  public function __construct($rows, int $numRows)
  {
    if(is_string($rows)){
      $this->result = $rows;
    } else {
      $this->rows = $rows;
    }
    $this->numRows = $numRows;
  }

  public function getRowByIndex(int $index)
  {
    return $this->rows[$index];
  }

  public function getRowsByFieldValue(string $field, string $value)
  {
    array_push($this->variables, $field, $value);
    $oldArray = $this->variables;
    $result = [];

    $this->iterate(function($key, $value){
      foreach($value as $field => $val){
        if($field === $this->variables[0] && $val === $this->variables[1]) {
          array_push($this->variables, $this->rows[$key]);
        }
      }  
    });

    foreach($this->array_diff_recursive($this->variables, $oldArray) as $newVariables){
      array_push($result, $newVariables);
    }

    $this->variables = [];
    return $result;
  }

  public function getFields()
  {

  }

  public function getRows()
  {
    return $this->rows;
  }

  public function getMessage()
  {
    if(isset($this->result)){
      return $this->result;
    } elseif (is_array($this->rows) && isset($this->rows)) {
      return true;
    }
    return null;
  }

  public function iterate(callable $function, bool $recursive = false, ...$variables)
  {
    if(isset($this->result)){
      $function($this->result);
    } else {
      foreach($this->rows as $index => $row){
        if($recursive){
          foreach($row as $key => $value){
            $function($key, $value);
          }
        }else {
          $function($index, $row);
        }
      }
    }
  }
}