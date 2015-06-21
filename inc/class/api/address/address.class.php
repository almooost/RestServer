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

  
  /**
   * Takes a mixed variable and returns the appropiate object for it
   * String = Address Tree
   * ID = Single Address
   * @param Hash $request Assoc Array of Request
   */
  public function __construct()
  {
  }

  /**
   * Public Method for fetching an Address
   * @param  string|int $m_id Mixed Value String|Int
   * @return void
   */
  public static function read($request)
  {
    if(preg_match('/^(\w+)$/', $request->getPath()))
      $this->readAll($request->getPath());
    else
      $this->readOne($request->getPath());
  }

  /**
   * Read all addresses from a given parent tre
   * @param  String $s_group_tree Parent Tree (Group)
   * @return AddressCollection Object   Addresses in given Group
   */
  private static function readAll($s_group_tree)
  {
    return new \api\address\AddressCollection($s_group_tree);
  }

  /**
   * Read a single Address
   * @param  int $i_id Address id
   * @return AddressEntity Object
   */
  private static function readOne($i_id)
  {
    return new \api\address\AddressEntity($i_id);
  }

  public static function create($h_data)
  {
    if(is_array($h_data) && count($h_data) >= 1)
      if(!isset($h_data['AddressGroup']))
        return \api\address\AddressEntity::create($h_data);
      else
        return \api\address\AddressCollection::create($h_data);
  }

  public static function update($h_data)
  {
    if(is_array($h_data) && count($h_data) >= 1)
      if(!isset($h_data['AddressGroup']))
        return \api\address\AddressEntity::update($h_data, $h_data['AddressGroupID']);
      else
        return \api\address\AddressCollection::update($h_data, $h_data['AddressGroupID']);
  }

  public static function remove($m_value)
  {
    if(is_array($m_value) && count($m_value) >= 1)
      if(!isset($m_value['AddressGroup']))
        return \api\address\AddressEntity::remove($m_value);
      else
        return \api\address\AddressCollection::remove($m_value);
  }

}


?>