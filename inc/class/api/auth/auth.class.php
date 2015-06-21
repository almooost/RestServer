<?php
/**
 * Default class for authentication
 * @author Sam Alfano
 * @date 13.06.15
 * @version v0.1
 */

namespace api\auth;

class Auth
{
  private $i_id;
  private $s_username;
  private $s_password;
  private $b_status;
  private $s_auth;


  public function __construct($obj_request)
  {
    $this->i_id = -1;
    $this->s_username = null;
    $this->s_password = null;
    $this->b_auth = false;

    if($obj_request->usesAuth())
      $this->setCredentials($obj_request);

    $this->auth();
  }

  /**
   * Authenticate user against DB
   * @return boolean success|failed
   */
  public function auth()
  {
    global $db;
    if(isset($this->s_username) && isset($this->s_password) )
    {
      $s_query = "SELECT `p_id`, `p_password` FROM `permissions` WHERE `p_username` = '".addslashes($this->s_username)."'";
      $h_sqlres = $db->fetchAssoc($s_query);
      if(isset($h_sqlres['data'][0]['p_password']) && $this->s_password === $h_sqlres['data'][0]['p_password'])
      {
        $this->b_auth = true;
        $this->i_id = $h_sqlres['data'][0]['p_id'];
      }
      else
        $obj_response = new \utility\responseController(array("Error" => "Authentication failed"), 405);
    }
    else
      $obj_response = new \utility\responseController(array("Error" => "Incomplete Request"), 405);
  }

  /**
   * Set Credentials to header information
   */
  private function setCredentials($obj_request)
  {
    $this->s_username = $obj_request->getRequest('PHP_AUTH_USER');
    $this->s_password = $obj_request->getRequest('PHP_AUTH_PW');
  }

  /**
   * Set Credentials to header information
   * @return hash of sql query
   */
  public function updatePassword($s_new_password)
  {
    global $db;
    if($this->b_auth && isset($s_new_password) && isset($this->i_id))
    {
      $s_query = "UPDATE `permissions` SET `p_password` = ".addslashes($s_new_password)." WHERE `p_id` = ".addslashes($this->i_id);
      $h_sqlres = $db->insert($s_query);

      return $h_sqlres;
    }
  }

  /**
   * Returns status of authentication
   * @return boolean True = authenticated
   */
  public function isAuth()
  {
    return $this->b_auth;
  }
}

?>