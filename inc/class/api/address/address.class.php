<?php
/**
 * Main Adress class, delivers address upon a request
 * @author Sam Alfano
 * @date 07.06.15
 * @version v0.1
 */

namespace api\address;

class Address 
{

  private $obj_entity;
  private $obj_collection;

  
  /**
   * Takes a mixed variable and returns the appropiate object for it
   * String = Address Tree
   * ID = Single Address
   * @param Hash $request Assoc Array of Request
   */
  public function __construct($request)
  {
    if(preg_match('/^(\w+)$/', $request->getPath()))
      $this->fetchAll($request->getPath());
    else
      $this->fetchOne($request->getPath());
  }


  public function fetchAll($s_parent_tree)
  {
    $obj_collection =  new \api\address\AddressCollection($s_parent_tree);
    return $obj_collection;
  }

  public function fetchOne($i_id)
  {
    $obj_entity = new \api\address\AddressEntity($i_id);
    return $obj_entity;
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