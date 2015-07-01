<?php
/**
 * Test Class for Address
 */
namespace test;

class cURLAuthTest extends \PHPUnit_Framework_TestCase
{

  protected $ch;
  
  protected function setUp()
  {
    $this->ch = curl_init('http://127.0.0.1/address/');
    //curl_setopt($this->ch, CURLOPT_URL, 'http://api.local/address/');
    curl_setopt($this->ch, CURLOPT_PORT, 8000);
  }

  public function testAuth()
  {
    curl_setopt($this->ch, CURLOPT_POST, true);
    curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($this->ch, CURLOPT_USERPWD, 'app1:supersecretpassword');
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));


   $this->assertEquals(true,curl_exec($this->ch));
    curl_close($this->ch);
  }
}

?>