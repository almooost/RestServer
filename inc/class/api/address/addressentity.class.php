<?php
/**
 * Represents a single address entity
 * @author Sam Alfano
 * @date 15.06.15
 * @version v0.2
 */

namespace api\address;

class AddressEntity implements \interfaces\Response
{
  private $b_status;
  private $h_address_data;
  private $h_rtn_data = array("status" => -1,
                              "data"   => "");
  private static $h_address_keys = array("addr_id"       => "AddressID",
                                        "addr_g_id"     => "AddressGroupID",
                                        "addr_title"    => "Title", 
                                        "addr_fname"    => "Firstname",
                                        "addr_lname"    => "Lastname",
                                        "addr_street"   => "Street",
                                        "addr_zip"      => "ZIP",
                                        "addr_location" => "Location",
                                        "addr_country"  => "Country",
                                        "addr_phone"    => "Phone",
                                        "addr_email"    => "EMail",
                                        "addr_status"   => "Status");

  /**
   * Create a new single Address object
   * @param hash $h_data Hash array of address information
   */
  public function __construct($h_data = array())
  {
    $this->b_status = false;
    if(is_array($h_data))
      $this->h_address_data = $h_data;
    else
      $this->h_address_data = null;
    $this->h_rtn_data = array();
  }


  /**
   * Get DB matching fields
   * @return hash Address fileds `column` => API Name
   */
  public static function getDBFields($s_values = "all")
  {
    if($s_values == 'keys')
      return array_keys(self::$h_address_keys);
    else if($s_values == 'values')
      return array_values(self::$h_address_keys);
    else
      return self::$h_address_keys;
  }

  /**
   * Read an exisint entry from the DB
   * @param  hash $s_search_value Searching value
   * @param  hash $s_search_attr  Searching attribute
   * @return hash                 AddressEntity | false
   */
  public function read($s_search_value, $s_search_attr = "AddressID")
  {
    global $db;
    $s_addr_query = "";
    if($s_search_value == '' || !array_search($s_search_attr, self::getDBFields()))
      return false;

    if(preg_match('/^(\d+)$/', $s_search_value) && $s_search_attr == "AddressID")
      $s_addr_query = "addr_id = ".addslashes(trim($s_search_value));

    else if(preg_match('/^([\w\d\_\-\s]+)$/', $s_search_value) )
      $s_addr_query = "`".addslashes(array_search(trim($s_search_attr), self::getDBFields()) )."` LIKE '%".addslashes(trim($s_search_value))."%'";
    else
      return false;

    $s_query = "SELECT `".implode('`, `',self::getDBFields('keys'))."` FROM `addresses` "
              ." WHERE `addr_status` = 1 AND ".$s_addr_query;
    
    $h_sqlres = $db->fetchAssoc($s_query);
    //var_dump($h_sqlres);
    if($h_sqlres['status'] == 1 && count($h_sqlres['data']) >= 1)
    {
      $a_address_entities = array();
      if(is_int(key($h_sqlres['data'])))
      {
        foreach ($h_sqlres['data'] as $m_key => $m_value)
        {
          if(is_array($m_value))
            array_push($a_address_entities,new \api\address\AddressEntity($m_value));
        }
        $obj_addr_collection = new \api\address\AddressCollection($a_address_entities);
        $obj_addr_collection->toArray();
        $obj_addr_collection->finalize(200);
      }
      else
      {
        $obj_addr_entity =  new \api\address\AddressEntity($h_sqlres);
        $obj_addr_entity->toArray();
        $obj_addr_entity->finalize(200);
      }
      return true;
    }
    return false;
  }

  /**
   * Create new address by an given array
   * @param  int $i_group_id  AddressGroupID
   * @return boolean              TRUE|FALSE
   */
  public function create()
  {
    global $db;
    //var_dump($this->h_address_data);
    if(is_null($this->h_address_data) || count($this->h_address_data) < 1 )
      return false;

    $a_query_data = $this->dbPrepare("create");
    //var_dump($a_query_data);
    if(!is_array($a_query_data) || count($a_query_data) < 1)
      return false;

    if(is_int(key($a_query_data)))
    {
      $s_query = "";
      foreach ($a_query_data as $i_key => $a_addr_data) 
        $s_query .= "INSERT INTO `addresses` (`".implode('`,`',array_keys($a_addr_data))."`) VALUES('".implode('\',\'',array_values($a_addr_data))."')";
    }
    else
      $s_query = "INSERT INTO `addresses` (`".implode('`,`',array_keys($a_query_data))."`) VALUES('".implode('\',\'',array_values($a_query_data))."')";
    $h_sqlres = $db->insert($s_query);
    if($h_sqlres['status'] == 1)
    {
      $this->h_rtn_data['status'] = 1;
      $this->h_rtn_data['data'] = $h_sqlres['id'];
      return $this->h_rtn_data;
    }
    return false;
  }

  /**
   * Disable a specific address by its id
   * @param  hash $h_addr_id    Hash Array of ID ['AddressID']
   * @return boolean            TRUE|FALSE
   */
  public function remove($h_addr_id)
  {
    global $db;
    if(!is_array($h_addr_id) || !isset($h_addr_id['AddressID']) || !preg_match('/^(\d+)$/', $h_addr_id['AddressID']) || $h_addr_id['AddressID'] < 1)
      return false;

    $s_query = "UPDATE `addresses` SET `addr_status` = -1 WHERE `addr_id` = ".addslashes(trim($h_addr_id['AddressID']));
    $h_sqlres = $db->update($s_query);
    if($h_sqlres['status'] == 1)
    {
      $this->h_rtn_data['status'] = 1;
      $this->h_rtn_data['data'] = $h_sqlres['id'];
      return true;
    }
    return false;
  }

  /**
   * Update an existig address in the DB
   * @param  int $i_id          ID of updated element
   * @return boolean            TRUE|FALSE
   */
  public function update()
  {
    global $db;
    if(isset($this->h_address_data['AddressID']) &&!preg_match('/^(\d+)$/', $this->h_address_data['AddressID']))
      return false;
    if(!is_array($this->h_address_data) || count($this->h_address_data) < 1)
      return false;
    $a_query_data = self::dbPrepare("update");

    if(!is_array($a_query_data) || count($a_query_data) < 1)
      return false;

    // Check if there are multiple addresses to update
    if(is_int(key($a_query_data)))
    {
      $s_query = "";
      foreach ($a_query_data as $i_key => $a_addr_data) 
      {
        $s_addr_query = array_walk($a_addr_data, function(&$s_value, $s_key)
          {
            $s_value = "`".$s_key."` = '".$s_value."'";
          });
        $s_query .= "UPDATE `addresses` SET ".implode(',',$a_addr_data)." WHERE ".$a_query_data['addr_id']."; ".PHP_EOL;
      }
    }
    // Update single address
    else
    {
      $s_addr_query = array_walk($a_query_data, function(&$s_value, $s_key)
      {
        $s_value = "`".$s_key."` = '".$s_value."'";
      });
      if(!isset($a_query_data['addr_id']))
        return false;
      $s_query = "UPDATE `addresses` SET ".implode(',',$a_query_data)." WHERE ".$a_query_data['addr_id']."; ".PHP_EOL;
    }
    $h_sqlres = $db->update($s_query);
    if($h_sqlres['status'] == 1)
    {
      $this->h_rtn_data['status'] = 1;
      $this->h_rtn_data['data'] = $h_sqlres['id'];
      return true;
    }

    return false;
    
  }

  /**
   * Prepare data and match to DB colums
   * @param  hash $this->h_address_data Given addresses
   * @param  int  $i_group_id  AddressGroupID Default: null
   * @return hash              Hash Array(DB_Column_name => Value)
   */
  public function dbPrepare($s_prepare_type = "create", $i_group_id = null)
  {
    $i_group_id = (!is_null($i_group_id) && is_int($i_group_id)) ? $i_group_id : null;
    $a_query_data = array();
    // Check if there are multiple addresses
    if(is_int(key($this->h_address_data)))
    {
      foreach ($this->h_address_data as $i_key => $h_address) 
      {
        $a_query_data[$i_key] = array();
        foreach (self::getDBFields() as $s_db_column => $s_readable_attr) 
        {
          if(isset($this->h_address_data[$s_readable_attr]))
          {
            // Match readable attributes
            if($s_prepare_type == "create" && $s_readable_attr == "AddressID")
              continue;

            else if ($i_group_id != null && $s_readable_attr == "AddressGroupID")
              $a_query_data[$i_key][$s_db_column] = $i_group_id;
            else
              $a_query_data[$i_key][$s_db_column] = $this->h_address_data[$s_readable_attr];
            continue;
          }
          else
            if(!isset($a_query_data[$i_key][$s_db_column]))
              $a_query_data[$i_key][$s_db_column] =  "";
        }
      }
    }
    // prepare for single address
    else
    {
      foreach (self::getDBFields() as $s_db_column => $s_readable_attr) 
      {
        foreach ($this->h_address_data as $s_attr => $s_addr_value) 
        {
          // Match readable attributes
          if($s_readable_attr == $s_attr)
          {
            if($s_prepare_type == "create" && $s_readable_attr == "AddressID")
              continue;
            else if ($i_group_id != null && $s_readable_attr == "AddressGroupID")
              $a_query_data[$s_db_column] = $i_group_id;
            else
              $a_query_data[$s_db_column] = $s_addr_value;
            continue;
          }
          else
            if($s_readable_attr == "AddressID")
              continue;
            else if(!isset($a_query_data[$s_db_column]))
              $a_query_data[$s_db_column] =  "";
        }
      }
    }
    return $a_query_data;
  }

  /**
   * Formats the Object that it is represented by a specific type
   * Implemented from Response interface
   */
  public function toArray()
  {
    $i_counter = 0;
    if(isset($this->h_address_data) && is_array($this->h_address_data))
      foreach ($this->h_address_data as $m_key => $m_value)
      {
        foreach (self::getDBFields() as $s_key => $s_value) 
          if($m_key == $s_key && is_string($s_value))
            $this->h_rtn_data[$s_value] = $m_value;
        $i_counter++;
      }
    return $this->h_rtn_data;
  }

  /**
   * Finalize data and send back to requester
   * Implemented from Response interface
   * @return reponseController Object
   */
  public function finalize($i_status_code)
  {
    if(preg_match('/(\d{3})/', $i_status_code))
      $obj_response = new \utility\responseController($this->h_rtn_data,$i_status_code);
  }

}


?>