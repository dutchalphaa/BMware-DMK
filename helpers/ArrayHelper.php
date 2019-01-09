<?php
/**
 * @package BMware DMK
 */

namespace helpers;

trait ArrayHelper
{
  private function array_diff_recursive($arr1, $arr2)
  {
    $outputDiff = [];
  
    foreach ($arr1 as $key => $value)
    {
      if (array_key_exists($key, $arr2))
      {
        if (is_array($value))
        {
          $recursiveDiff = array_diff_recursive($value, $arr2[$key]);
  
          if (count($recursiveDiff))
          {
            $outputDiff[$key] = $recursiveDiff;
          }
        }
        else if (!in_array($value, $arr2))
        {
          $outputDiff[$key] = $value;
        }
      }
      else if (!in_array($value, $arr2))
      {
        $outputDiff[$key] = $value;
      }
    }
  
    return $outputDiff;
  }
}