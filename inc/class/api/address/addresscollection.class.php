<?php
/**
 * Represents a whole address collection for a address tree
 * @author Sam Alfano
 * @date 15.06.15
 * @version v0.2
 */

namespace api\address;

class AddressCollection implements \interfaces\Response
{

  private $a_address_entities;
  private $h_rtn_data;

  public function __construct($s_address_tree)
  {
    $this->a_address_entities = array();
    $this->h_rtn_data = array();
    $this->getAdresses($s_address_tree);
  }

  public function getAdresses($m_address_tree)
  {
    global $db;
    if(!preg_match('/^(\d+)$/', $m_address_tree))
      $s_query = "SELECT * FROM `addresses` INNER JOIN `address_parents` USING(`addr_p_id`) WHERE `addr_p_name` = '".addslashes($m_address_tree)."'";
    else
      $s_query = "SELECT * FROM `addresses` WHERE `addr_p_id` = ".addslashes($m_address_tree);
    $h_sqlres = $db->fetchAssoc($s_query);
    if($h_sqlres != -1 && is_array($h_sqlres))
    {
      if(is_int(key($h_sqlres)))
      {
        foreach ($h_sqlres as $m_key => $m_value)
          if(is_array($m_value))
            $this->a_address_entities[] = new AddressEntity($m_value);
      }
      else
        $this->a_address_entities = new AddressEntity($h_sqlres);
    }
    /*
    if($h_sqlres == -1)
      return $h_sqlres;
    foreach ($h_sqlres as $s_key => $h_value) 
      $this->a_address_entities[$s_key] = $h_value;
    */
   
    if($this->toArray())
      $this->finalize(200);
    else
      $this->finalize(204);
  }
  /**
   * Formats the Object that it is represented by a specific type
   * Implemented from Response interface
   */
  public function toArray()
  {
    $this->h_rtn_data = array();
    if(is_array($this->a_address_entities))
      foreach ($this->a_address_entities as $i_key => $obj_address_entity) 
        if(is_a($obj_address_entity, "AddressEntity"))
          $this->h_rtn_data[] = $obj_address_entity->toArray();

    if(!empty($this->h_rtn_data))
      return true;
    return false;
  }

  /**
   * Formats the data of the object and passes it to the responseController for sending back
   */
  public function finalize($i_status_code)
  {
    if(preg_match('/(\d{3})/', $i_status_code))
      $obj_response = new \utility\responseController($this->h_rtn_data,$i_status_code);
  }

}


?>