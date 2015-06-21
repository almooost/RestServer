<?php
/**
 * Provides methods for conversion between arrays and json objects
 * @author Sam Alfano (c) 2015
 * @date 20.06.15
 * @version v0.1
 */

namespace utility;

class phpJSON
{
  private $s_json;
  private $h_php;

  /**
   * Creates a new phpJSON object
   * @param mixed $m_data Mixed Input JSON|PHP Array
   */
  public function __construct($m_data)
  {
    $s_type         = "php";
    $this->h_php    = array();
    $this->s_json   = "";


    if(is_array($m_data))
      $s_type = "php";
    else if (is_object($m_data))
      $s_type = "json";
    $this->prepare($m_data, $s_type);
  }

  /**
   * Prepare incoming Data for processing
   * @param   mixed  $m_data  Mixed Data JSON|PHP Array
   * @param   string $s_type  Data Type information
   * @return  void
   */
  private function prepare($m_data, $s_type)
  {
    if($s_type == "php")
    {
      if(count($m_data) >= 1 && is_int(key($m_data)))
        $this->h_php = $m_data;
      else if(preg_match('/^[\w\d\_\-]+$/'))
        array_push($this->h_php, $m_data));
      else
        $this->h_php = $m_data;

      $this->s_json = self::ArraytoJSON($this->h_php);
    }
    else if($s_type == "json")
    {
      $this->s_json = $m_data;
      $this->h_php  = self::JSONtoArray($this->s_json);
    }


  }
  
  /**
   * (Static) Transform a array to a json object string
   * @param  array $a_array        Input PHP Array
   * @param  string $s_json_option JSON Option, Default: JSON_FORCE_OBJECT
   * @return string                JSON object string
   */
  public static function ArraytoJSON($a_array, $s_json_option = "JSON_FORCE_OBJECT")
  {
    return json_encode($a_array, $s_json_option);
  }

  /**
   * (Static) Convert a json object to a php string
   * @param  string  $s_json   JSON Object
   * @param  boolean $b_assoc  Return associative Array, Default: false
   * @return array             PHP array
   */
  public static function JSONtoArray($s_json, $b_assoc = false)
  {
    return json_decode($s_json,$b_assoc);
  }
}

?>