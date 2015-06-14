<?php
/**
 * Provides a gerneral response for the given data
 * @author Sam Alfano
 * @date 14.06.15
 * @version v0.1
 */

namespace utility;

class responseController
{

  private $h_data;
  private $s_payload;
  private $i_status_code;
  private $s_content_type;

  private $h_codes = array("200" => "200 OK",
                          "201" => "201 Created",
                          "204" => "204 No Content",
                          "400" => "400 Bad Request",
                          "404" => "404 Not Found",
                          "405" => "405 Method Not Allowed",
                          "406" => "406 Not Acceptable",
                          "500" => "500 Internal Server Error",
                          "501" => "501 Not Implemented");

  /**
   * Create a new reponse object
   * @param hash $h_response_array All provided response data
   * @param string $s_content_type      needed Content Type
   */
  public function __construct($h_response_array, $i_status_code = 200, $s_content_type = "text/json")
  {
    $this->h_data         = $h_response_array;
    $this->s_payload      = "";
    $this->i_status_code  = $i_status_code;
    $this->s_content_type = $s_content_type;
    $this->createResponse();
  }


  /**
   * Create response from data
   */
  public function createResponse()
  {
    if(!is_array($this->h_data) || $this->s_content_type == "")
      $this->errorResponse();
    $this->s_payload = json_encode($this->h_data);

    $a_response = array($this->h_codes[$this->i_status_code]);
    $this->sendResponse($a_response);
    
  }

  /**
   * Create error response if there was an error
   */
  public function errorResponse()
  {
    $a_response = array($this->h_codes[400],
                        "Description: Error during request");

    $this->h_data["Error"] = "No Data";
    $this->sendResponse($a_response);
  }

  /**
   * Final Function to send response back
   * @param  string $s_response Response details
   * @return nothing
   */
  public function sendResponse($a_response)
  {
    foreach ($a_response as $i_key => $s_value) 
      header($s_value);
    header("Content-Type:".$this->s_content_type);
    $this->formatData();
      echo($this->s_payload);
    exit(1);
  }

  public function formatData()
  {
    if(isset($this->s_content_type) && preg_match('/^text\/[json|xml]$/', $this->s_content_type))
    {
      if(preg_match('/^text\/json$/', $this->s_content_type))
        $this->s_payload = json_encode($this->h_data,JSON_FORCE_OBJECT);
      else if(preg_match('/^text\/xml$/', $this->s_content_type)) 
        $this->s_payload = \utility\XML::encodeObj($this->h_data);
      else
        $this->s_payload = '{"Error" : "Wrong Content-Type"}';
    }
  }

}

?>