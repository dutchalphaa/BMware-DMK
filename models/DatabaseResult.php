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
  public $numRows;
  public $useModified = false;
  private $modifiedRows = [];
  private $variables = [];
  private $access = false;
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

  public function getUseModifeid()
  {
    return $this->useModified;
  }

  public function getRowByIndex(int $index)
  {
    if($this->useModified){
      $rows = $this->modifiedRows;
      $this->modifiedRows = [];
    } else {
      $rows = $this->rows;
    }

    $this->modifiedRows = $rows[$index];
    return $this;
  }

  public function getRowsByFieldValue(string $field, string $value)
  {
    array_push($this->variables, $field, $value);
    $oldArray = $this->variables;

    $this->iterate(function($index, $row, $modifiedRows){
      foreach($row as $field => $value){
        if($field === $this->variables[0] && $value === $this->variables[1]) {
          array_push($this->modifiedRows, $this->rows[$index]);
        }
      }  
    });

    return $this;
  }

  public function getFields(string ...$fields)
  {
    array_push($this->variables, ...$fields);
    $oldArray = $this->variables;

    $this->iterate(function($index, $row, $modifiedRows){
        $this->modifiedRows[$index] = [];

        foreach ($row as $field => $value) {
          if(in_array($field, $this->variables)){
            $this->modifiedRows[$index][$field] = $value;
          }
        }
    });

    return $this;
  }

  public function getRows()
  {
    if($this->useModified){
      $rows = $this->modifiedRows;
    } else {
      $rows = $this->rows;
    }

    $this->variables = [];
    $this->modifiedRows = $this->rows;

    return $rows;
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
    if($this->useModified){
      $rows = $this->modifiedRows;
      $this->modifiedRows = [];
    }else {
      $rows = $this->rows;
    }

    $this->access = true;

    if(isset($this->result)){
      $function($this->result);
    } else {
      foreach($rows as $index => $row){
        if($recursive){
          foreach($row as $key => $value){
            $function($key, $value, [$this, "context"]);
          }
        }else {
          $function($index, $row, [$this, "context"]);
        }
      }
    }

    $this->access = false;
    $this->variables = [];  
  }

  public function context(string $action = "" , ...$params)
  {
    if(!$this->access){
      if($action != "variables"){
        throw new \Exception("this function can only be used outside of iterate to set variables for iterate");
      } 
    }

    if(empty($params)){
      if($action === "variables"){
        return $this->variables;
      } else {
        return $this->modifiedRows;
      }
    } else {
      if($action === "variables"){
        array_push($this->variables, ...$params);
      } elseif($action === "push"){
        array_push($this->modifiedRows, ...$params);
      } elseif ($action === "set") {
        if(isset($params[1])){
          $this->modifiedRows[$params[1]] = $params[0]; 
        } else {
          $this->modifiedRows = $params[0];
        }
      }
    }
  }

  public function useVariables(...$params)
  { 
    array_push($this->variables, ...$params);
    return $this;
  }

}