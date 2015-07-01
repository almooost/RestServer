<?php
/**
 * Parses a given uri and deliver its method and path
 * @author Sam Alfano
 * @date 13.06.15
 * @version v0.1
 */
namespace utility;

class requestController
{
  private $s_method;
  private $a_path;
  private $b_auth;
  private $h_request;

  /**
   * Gets the global server variable from the the main file
   * and extracts its variables
   */
  public function __construct($h_server)
  {
    if(is_array($h_server) && isset($h_server['REQUEST_METHOD']) && isset($h_server['REQUEST_URI']))
    {
      (isset($h_server['REQUEST_METHOD']))  ? $this->s_method         = $h_server['REQUEST_METHOD'] : $this->s_method        = null;
      (isset($h_server['REQUEST_URI']))     ? $this->a_path           = explode("/",$h_server['REQUEST_URI'])    : $this->a_path          = null;
      // create authorization
      (isset($h_server['PHP_AUTH_USER']) && isset($h_server['PHP_AUTH_PW'])) ? $this->b_auth = true : $this->b_auth = false;
      $this->h_request = $h_server;
    }
  }
  /**
   * Get Method of request
   * @return String Method
   */
  public function getMethod()
  {
    return $this->s_method;
  }

  /**
   * Get path of request
   * @return String path
   */
  public function getPath($i_index = -1)
  {
    if($i_index === -1)
      return end($this->a_path);
    else if ($i_index == "full")
      return implode("/",$this->a_path);
    return $this->a_path;
  }

  /**
   * Check if request uses authorization
   * @return boolean
   */
  public function usesAuth()
  {
    return $this->b_auth;
  }

  /**
   * Get whole request
   * @return Hash request
   */
  public function getRequest($s_key = null)
  {
    if($s_key != null && key_exists($s_key, $this->h_request))
      return $this->h_request[$s_key];
    return $this->h_request;
  }

  public function getPayload()
  {
    if($this->s_method == "GET")
      return $_GET;
    else if($this->s_method == "POST" || $this->s_method == "PUT" || $this->s_method == "DELETE")
    {
      parse_str(file_get_contents("php://input"),$data);
      return (array)$data;
    }
    else
      return false;
  }

  /**
   * Check if request has a valid method and path
   * @return boolean TRUE|FALSE
   */
  public function isValid()
  {
    if($this->b_auth 
      && isset($this->s_method) && $this->s_method != ''
      && isset($this->a_path) && $this->a_path != '')
      return true;
    return false;
  }

}