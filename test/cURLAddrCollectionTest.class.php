<?php
/**
 * Test Class for Address
 */
namespace test;

class cURLAddrCollectionTest extends \PHPUnit_Framework_TestCase
{

  protected $ch;
  protected $s_get_data;
  protected $s_post_data;
  protected $s_put_data;
  protected $s_delete_data;

  protected function setUp()
  {
    // Initialize data
    $this->s_get_data  = "firm";
    $this->s_post_data = http_build_query(array("AddressGroupID" => 3, 
                                               "AddressGroupName" => "firm",
                                               "AddressGroupDescription" => "Test Firm Description" ));

    $this->s_put_data  = http_build_query(array("AddressGroupID" => null, 
                                               "AddressGroupName" => "cURLFirm",
                                               "AddressGroupDescription" => "cURL Test Description" ));

    //$this->s_delete_data = http_build_query(array('AddressGroupID' => 3));

    $this->ch = curl_init('http://127.0.0.1/address/group/');
    //curl_setopt($this->ch, CURLOPT_URL, 'http://api.local/address/');
    curl_setopt($this->ch, CURLOPT_PORT, 8000);
    curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($this->ch, CURLOPT_USERPWD, 'app1:supersecretpassword');
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
  }

  public function testAddrCollectionGET()
  {
    curl_setopt($this->ch, CURLOPT_HTTPGET, true);
    curl_setopt($this->ch, CURLOPT_URL, 'http://127.0.0.1/address/group/firm');
    $this->assertInternalType('string', curl_exec($this->ch));
    curl_close($this->ch);
  }

  public function testAddrCollectionPOST()
  {
    curl_setopt($this->ch, CURLOPT_POST, count($this->s_post_data));
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->s_post_data);
    $result = curl_exec($this->ch);
    $this->assertEquals(1, preg_match('/(\"status\"\:1)/',$result));
    curl_close($this->ch);
  }

  public function testAddrCollectionPUT()
  {
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS,$this->s_put_data);
    $result = curl_exec($this->ch);
    $this->assertEquals(1, preg_match('/(status\"\:1.*\"data\"\:\"\d+\")/',$result));
    $a_matches = array();
    preg_match('/(\"data\"\:\"(\d+)\")/',$result, $a_matches);
    curl_close($this->ch);
    return http_build_query(array('AddressGroupID' => $a_matches[2]));
  }

  /**
   * @depends testAddrCollectionPUT
   */
  public function testAddrCollectionDELETE($s_delete_data)
  {
    curl_setopt($this->ch, CURLOPT_URL, 'http://127.0.0.1/address/group/');
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS,$s_delete_data);
    $result = curl_exec($this->ch);
    $this->assertEquals(1, preg_match('/(\"status\"\:1)/',$result));
    curl_close($this->ch);
  }
}

?>