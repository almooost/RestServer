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
  private $h_address_keys = array("addr_id"       => "ID",
                                  "addr_p_id"     => "Parent ID",
                                  "addr_title"    => "Title", 
                                  "addr_fname"    => "Firstname",
                                  "addr_lname"    => "Lastname",
                                  "addr_street"   => "Street",
                                  "addr_zip"      => "ZIP",
                                  "addr_location" => "Location",
                                  "addr_country"  => "Country",
                                  "addr_phone"    => "Phone",
                                  "addr_email"    => "EMail");

  public function __construct($h_data)
  {
    $this->b_status = false;
    $this->a_address_data = null;
    $this->h_rtn_data = array();

    if($h_data != -1 && is_array($h_data))
      $this->a_address_data = $h_data;
  }

  public function get($m_key)
  {
    if($this->a_address_data != null && is_array($this->a_address_data) && array_key_exists($m_key,$this->a_address_data))
      return $this->a_address_data[$m_key];
    else
    {
      if(is_array($this->a_address_data))
        foreach ($this->a_address_data as $m_index => $m_value) 
          if(is_array($m_value) && array_key_exists($m_key))
            return $m_value[$m_key];
    }
    return false;
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
        foreach ($h_address_keys as $s_key => $s_value) 
          if($m_key == $s_key && is_string($s_value))
            $this->h_rtn_data[$i_counter][$s_key] = $m_value;
          else if(is_array($m_value))
            foreach ($m_value as $s_index => $s_info)
              if($s_index == $s_key && is_string($s_info))
                $this->h_rtn_data[$i_counter][$s_key] = $s_indo;

        $i_counter++;
      }
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