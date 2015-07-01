<?php
/**
 * Test Class for Address
 */
namespace test;

class cURLAddrEntityTest extends \PHPUnit_Framework_TestCase
{

  protected $ch;
  protected $s_get_data;
  protected $s_post_data;
  protected $s_put_data;
  protected $s_delete_data;

  protected function setUp()
  {
    // Initialize data
    $this->s_get_data = 1;
    $this->s_post_data = http_build_query(array("AddressID" => 4,
                                                "AddressGroupID" => 1,
                                                "Title" => "Mr.",
                                                "Firstname" => "Dan",
                                                "Lastname" => "Doe",
                                                "Street" => "Freestyle.59",
                                                "ZIP" => "900",
                                                "Location" => "St.Gallen",
                                                "Country" => "Switzerland",
                                                "Phone" => "079 708 84 68",
                                                "EMail" => "dan.doe@gmail.com",
                                                "Status" => 1));

    $this->s_put_data = http_build_query(array("AddressID" => 6,
                                              "AddressGroupID" => 1,
                                              "Title" => "Dr.",
                                              "Firstname" => "Samuel",
                                              "Lastname" => "Alfano",
                                              "Street" => "Untersee.59",
                                              "ZIP" => "8640",
                                              "Location" => "Jona",
                                              "Country" => "Switzerland",
                                              "Phone" => "079 708 84 68",
                                              "EMail" => "sam.almost@gmail.com",
                                              "Status" => 1));

    $this->s_delete_data = http_build_query(array("AddressID" => 6));


    $this->ch = curl_init('http://127.0.0.1/address/firm');

    curl_setopt($this->ch, CURLOPT_PORT, 8000);
    curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($this->ch, CURLOPT_USERPWD, 'app1:supersecretpassword');
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
  }

  public function testAddrEntityGET()
  {
    curl_setopt($this->ch, CURLOPT_HTTPGET, true);
    curl_setopt($this->ch, CURLOPT_URL, 'http://127.0.0.1/address/3');
    $this->assertInternalType('string', curl_exec($this->ch));
    curl_close($this->ch);
  }

  public function testAddrEntityPOST()
  {
    curl_setopt($this->ch, CURLOPT_POST, count($this->s_post_data));
    curl_setopt($this->ch, CURLOPT_URL, 'http://127.0.0.1/address/');
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->s_post_data);
    $result = curl_exec($this->ch);
    $this->assertEquals(1, preg_match('/(\"status\"\:1)/',$result));
    curl_close($this->ch);
  }

  public function testAddrEntityPUT()
  {
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS,$this->s_put_data);
    $result = curl_exec($this->ch);
    $this->assertEquals(1, preg_match('/(status\"\:1.*\"data\"\:\"\d+\")/',$result));
    curl_close($this->ch);
  }

  public function testAddrEntityDELETE()
  {
    curl_setopt($this->ch, CURLOPT_URL, 'http://127.0.0.1/address/6');
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS,$this->s_delete_data);
    $result = curl_exec($this->ch);
    $this->assertEquals(1, preg_match('/(\"status\"\:1)/',$result));
    curl_close($this->ch);
  }
}

?>