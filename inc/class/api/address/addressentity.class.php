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
  private $a_address_data;
  private $h_rtn_data;
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
  public function __construct($h_data)
  {
    $this->b_status = false;
    $this->a_address_data = null;
    $this->h_rtn_data = array();

    if($h_data != -1 && is_array($h_data))
      $this->a_address_data = $h_data;
  }


  /**
   * Get DB matching fields
   * @return hash Address fileds `column` => API Name
   */
  public function getDBFields()
  {
    return self::$h_address_keys;
  }

  /**
   * Read an exisint entry from the DB
   * @param  hash $h_search Hash Array of sarch criterias
   * @return hash           AddressEntity | false
   */
  public static function read($h_search)
  {
    global $db;
    $s_addr_query = "";

    if(isset($h_data['search_value']) && preg_match('/^([\w\d\_\-]+)$/', $h_search['search_value'])
      && isset($h_data['search_attr']) && array_search($h_search['search_attr'], self::$h_address_keys) )
      $s_addr_query = "`".addslashes(array_search(trim($h_search['search_attr']), self::$h_address_keys ))."` " 
                     ." LIKE '%".addslashes(trim($h_search['search_value']))."%'";
    if($s_addr_query == "")
      return false;

    $s_query = "SELECT `".explode('`, `')array_keys(self::$h_address_keys)."` FROM `addresses` "
              ." WHERE ".addslashes($s_addr_query);
    $h_sqlres = $db->fetchAssoc($s_query);
    if($h_sqlres['status'] != -1 && is_array($h_sqlres['data']))
      return new AddressEntity($h_sqlres['data']);
    return false;
  }

  /**
   * Create new address by an given array
   * @param  hash $h_addresses Hash Array of addresses]
   * @param  int $i_group_id  AddressGroupID
   * @return boolean              TRUE|FALSE
   */
  public function create($h_addresses, $i_group_id)
  {
    global $db;
    if(!is_array($h_addresses) || count($h_addresses) < 1 )
      return false;

    $a_query_data = dbPrepare($h_addresses, $i_group_id);
    
    if(!is_array($a_query_data) || count($a_query_data) < 1)
      return false;

    $s_query = "";
    foreach ($a_query_data as $i_key => $a_addr_data) 
      $s_query .= "INSERT INTO `addresses` (`".explode(',`',array_keys($a_addr_data))."`) VALUES('".addslashes(explode(',\'',array_values($a_addr_data)))."')";
    $h_sqlres = $db->insert($s_query);
    if($h_sqlres['status'] == 1)
      return true;
    return false;
  }

  /**
   * Disable a specific address by its id
   * @param  int $i_addr_id     ID of address
   * @return boolean            TRUE|FALSE
   */
  public static function remove($i_addr_id)
  {
    global $db;
    if(!preg_match('/^(\d+)$/', $i_addr_id) && $i_addr_id < 1)
      return false;

    $s_query = "UPDATE `addresses` SET `addr_status` = -1 WHERE `addr_id` = ".addslashes(trim($i_addr_id));
    $h_sqlres = $db->update($s_query);
    if($h_sqlres['status'] == 1)
      return true;
  }

  /**
   * Update an existig address in the DB
   * @param  hash $h_addresses  Hash Array with address data
   * @param  int $i_id          ID of updated element
   * @return boolean            TRUE|FALSE
   */
  public static function update($h_addresses, $i_id)
  {
    global $db;
    if(!preg_match('/^(\d+)$/', $i_id))
      return false;
    if(!is_array($h_addresses) || count($h_addresses) < 1)
      return false;
    $a_query_data = self::dbPrepare($h_addresses);

    if(!is_array($a_query_data) || count($a_query_data) < 1)
      return false;

    $s_query = "";
    foreach ($a_query_data as $i_key => $a_addr_data) 
    {
      $s_addr_query = array_walk($a_addr_data, function(&$s_value, $s_key)
        {
          $s_value = "`".$s_key."` = '".$s_value."'";
        });
      $s_query .= "UPDATE `addresses` SET ".explode(',',$a_addr_data)." WHERE `addr_id` = ".addslashes($i_id)."; ".PHP_EOL;
    }
    $h_sqlres = $db->update($s_query);
    if($h_sqlres['status'] == 1)
      return true;

    return false;
    
  }

  /**
   * Prepare data and match to DB colums
   * @param  hash $h_addresses Given addresses
   * @param  int  $i_group_id  AddressGroupID Default: null
   * @return hash              Hash Array(DB_Column_name => Value)
   */
  public static function dbPrepare($h_addresses, $i_group_id = null)
  {
    $a_query_data = array();
    if(is_int(key($h_addresses)))
    {
      foreach ($h_addresses as $i_key => $h_address) 
      {
        $a_query_data[$i_key] = array();
        foreach (self::$h_address_keys as $s_column => $s_value) 
        {
          if(isset($h_addresses[$s_value]))
          {
            if($s_value == "AddressID")
              $a_query_data[$i_key][$s_column] = "NULL";
            else if ($i_group_id != null && $s_value == "AddressGroupID")
              $a_query_data[$i_key][$s_column] = $i_group_id;
            else
              $a_query_data[$i_key][$s_column] = $h_addresses[$s_value];
            continue;
          }
          else
            $a_query_data[$i_key][$s_column] =  "";
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
    if(isset($this->a_address_data) && is_array($this->a_address_data))
      foreach ($this->a_address_data as $m_key => $m_value)
      {
        foreach (self::$h_address_keys as $s_key => $s_value) 
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