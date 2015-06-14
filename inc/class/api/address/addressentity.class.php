<?php
/**
 * Represents a single address entity
 * @author Sam Alfano
 * @date 07.06.15
 * @version v0.1
 */

namespace api\address;

class AddressEntity
{

  private $a_address_data;

  public function __construct()
  {
    $a_address_data = array();
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