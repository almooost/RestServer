<?php
/**
 * Represents a whole address collection for a address tree
 * @author Sam Alfano
 * @date 15.06.15
 * @version v0.3
 */

namespace api\address;

class AddressCollection implements \interfaces\Response
{

  private $a_address_entities;
  private $h_rtn_data;

  // Mappings to the address_group table
  private $h_address_group_keys = array("addr_g_id"    => "AddressGroupID",
                                         "addr_g_name"  => "AddressGroupName",
                                         "addr_g_desc"  => "AddressGroupDescription",
                                         "addr_g_id"    => "AddressGroupStatus");

  /**
   * Create new AddressCollection object
   * @param string $s_group name of group
   */
  public function __construct()
  {
    $this->a_address_entities = array();
    $this->h_rtn_data = array();
//    $this->read($s_group);
  }


  /**
   * Read a group from DB by its ID|Name
   * @param  mixed $m_group Mixed Value ID|Name
   * @return boolen         TRUE|FALSE
   */
  public function read($m_group)
  {

    global $db;
    if(!preg_match('/^(\d+)$/', $m_group))
      $s_query = "SELECT `".explode('`, `')array_keys(addressEntity::$h_address_keys)."` FROM `addresses` " 
                ." INNER JOIN `address_groups` USING(`addr_g_id`) WHERE `addr_g_name` = '".addslashes($m_group)."' "
                ." AND `addr_g_status` = 1";
    else
      $s_query = "SELECT `".explode('`, `')array_keys(addressEntity::$h_address_keys)."` FROM `addresses` " 
                ." WHERE `addr_g_id` = ".addslashes($m_group)." AND `addr_g_status` = 1 ";
    $h_sqlres = $db->fetchAssoc($s_query);
    error_log($s_query);
    if($h_sqlres['data'] != -1 && is_array($h_sqlres['data']))
    {
      if(is_int(key($h_sqlres['data'])))
      {
        foreach ($h_sqlres['data'] as $m_key => $m_value)
          if(is_array($m_value))
            array_push($this->a_address_entities,new \api\address\AddressEntity($m_value));
      }
      else
        array_push($this->a_address_entities, new \api\address\AddressEntity($h_sqlres));
    }

    if($this->toArray())
      return true;
      //$this->finalize(200);
    else
      return false;
      //$this->finalize(204);
  }


  /**
   * Create new parent address tree and entries for it if provided
   * @param  hash $h_data    Hash Array with Parent Name as key
   * @return boolean         TRUE|FALSE
   */
  public static function create($h_data)
  {
    global $db;
    if(!isset($h_data['AddressGroupName']) || !preg_match('/^([\w\d\_\-]+)$/', $h_data['AddressGroupName']) || self::exists($h_data['AddressGroupName']))
      return false;

    $s_description = (isset($h_data['AddressGroupDescription']) && $h_data['AddressGroupDescription'] != "") ? trim($h_data['AddressGroupDescription']) : "";

    $s_query = "INSERT INTO `address_groups` VALUES(NULL, '".addslashes(trim($h_data['AddressGroupName']))."', '".addslashes($s_description)."', 1)";
    $h_sqlres = $db->insert($s_query);
    error_log($s_query);
    var_dump($h_sqlres);
    if($h_sqlres['status'] == 1 && preg_match('/^(\d+)$/', $h_sqlres['id']))
    {
      // Check if addresses were provided and let craete them
      if(isset($h_data['Addresses']) && is_array($h_data['Addresses']))
      {
        return true;
        //return \api\address\AddressEntity::create($h_data['Addresses'], $h_sqlres['id']);
      }
      return true;
    }
    return false;

  }

  /**
   * Remove a group and its childs by its name
   * @param  string $s_group_name Group name
   * @return boolean              TRUE|FALSE
   */
  public static function remove($s_group_name)
  {
    global $db;
    if(!preg_match('/^[\w\d\_\-]+$/', $s_group_name))
      return false;

    $s_query = "SELECT DISTINCT(`addr_g_id`) FROM `address_groups` WHERE `addr_g_name` = '".addslashes($s_group_name)."'";
    $h_sqlres = $db->fetchAssoc($s_query);
    error_log($s_query);
    if($h_sqlres['status'] != -1)
    {
      // Update Status
      $s_query = "UPDATE `address_groups` INNER JOIN `addresses` USING(`addr_g_id`) SET `addr_g_status` = -1 "
                ." WHERE `addr_g_id` = ".addslashes($h_sqlres['data'][0]['addr_g_id']);
      error_log($s_query);
      $h_sqlres = $db->update($s_query);
      if($h_sqlres['status'] != -1)
        return true;
    }
    return false;

  }

  /**
   * Update existing edited entries
   * @param  hash    $h_data Hash Array of Data to update
   * @return boolean         TRUE|FALSE
   */
  public static function update($h_data)
  {
    global $db;
    if(!is_array($h_data))
      return false;
      
    if(isset($h_data['AddressGroupID']) && preg_match('/^(\d+)$/', $h_data['AddressGroupID']))
      $s_group_where_query = " `addr_g_id` = ".addslashes($h_data['AddressGroupID']);
    else if(isset($h_data['AddressGroupName']) && preg_match('/^([\w\d\_\-]+)$/', $h_data['AddressGroupName']))
      $s_group_where_query = " `addr_g_name` = '".addslashes(trim($h_data['AddressGroupName']))."'";
    else
      return false;

    if(!isset($h_data['AddressGroupDescription'])) 
      return false;
       
    $s_query = "UPDATE `address_groups` SET `addr_g_desc` = '".addslashes(trim($h_data['AddressGroupDescription']))."' WHERE ".$s_group_where_query;
    $h_sqlres = $db->update($s_query);
    if($h_sqlres['status'] != -1)
      return true;
  }

  /**
   * Check if a group name already exsits in DB
   * @param  stirng $s_group_name  given name on insert
   * @return boolen                TRUE = exsits | False = not
   */
  public static function exists($s_group_name)
  {
    global $db;
    if(!preg_match('/^([\w\d\_\-]+)$/', $s_group_name))
      return true;

    $s_query = "SELECT DISTINCT(`addr_g_name`) FROM `address_groups` WHERE `addr_g_name` = '".addslashes(trim($s_group_name))."' WHERE `addr_g_status` = 1";
    $h_sqlres = $db->fetchAssoc($s_query);
    if($h_sqlres['status'] == 1 && count($h_sqlres['data']) == 0) 
      return false;
    return true;
  }


  /**
   * Formats the Object that it is represented by a specific type
   * Implemented from Response interface
   */
  public function toArray()
  {
    $this->h_rtn_data = array();
    if(is_array($this->a_address_entities) && count($this->a_address_entities) >= 1)
    {
      foreach ($this->a_address_entities as $i_key => $obj_address_entity) 
        if(is_a($obj_address_entity, "\api\address\AddressEntity"))
          array_push($this->h_rtn_data, $obj_address_entity->toArray());
    }

    if(count($this->h_rtn_data) >= 1)
      return true;
    return false;
  }

  /**
   * Formats the data of the object and passes it to the responseController for sending back
   */
  public function finalize($i_status_code)
  {
    if(preg_match('/^(\d{3})$/', $i_status_code))
      $obj_response = new \utility\responseController($this->h_rtn_data,$i_status_code);
  }


}


?>