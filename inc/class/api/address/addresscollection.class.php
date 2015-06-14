<?php
/**
 * Represents a whole address collection for a address tree
 * @author Sam Alfano
 * @date 07.06.15
 * @version v0.1
 */

namespace api\address;

class AddressCollection
{

  private $a_address_entities;

  public function __construct($s_address_tree)
  {
    $this->a_address_entities = array();
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
    if($h_sqlres == -1)
      return $h_sqlres;
    foreach ($h_sqlres as $s_key => $h_value) 
      $this->a_address_entities[$s_key] = $h_value;
    $this->finalize();
  }

  /**
   * Formats the data of the object and passes it to the responseController for sending back
   */
  public function finalize()
  {
    $obj_response = new \utility\responseController($this->a_address_entities,200);
  }

}


?>