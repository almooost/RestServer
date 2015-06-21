<?php
/**
 * The abstract API Handle class delivers the main Method for handling a a request
 * @author Sam Alfano
 * @date 13.06.15
 * @version v0.1
 */
namespace api;

abstract class APIHandle
{
  private $s_method;
  private $s_path;

  public function handle($s_method, $s_path = '')
  {
    $this->s_method  = $s_method;
    $this->s_path    = $s_path;
  }

  /**
   * Formats the Object that it is represented by a specific type
   * @param  string $s_type Return Type
   * @return String mixed type
   */
  public function format($s_type = "json")
  {
    $a_rtn = array();
    foreach ($this as $key => $value) 
    {
      array_push($a_rtn, $value);
    }

    if($s_type == "json")
      return json_encode($a_rtn, JSON_PRETTY_PRINT);
    else if ($s_type == "xml")
      return \utility\XML::encodeObj($this);
    else
      return $a_rtn;
  }
}


?>