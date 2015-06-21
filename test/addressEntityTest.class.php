<?php
/**
 * Test addressCollection class
 * @author Sam Alfano (c) 2015
 * @date 20.06.15
 * @version v0.1
 * @group address
 */
namespace test;

class addressEntityTest extends \PHPUnit_Framework_TestCase
{

  protected $s_json_collection;
  protected $s_post_collection;
  protected $s_put_collection;
  protected $m_delete_id;

  protected $obj_addr_collection;

  protected function setUp()
  {
    $this->s_json_collection = json_encode(array("AddressGroupID" => 1, 
                                                  "AddressGroupName" => "firm",
                                                  "AddressGroupDescription" => "New Firm Description" ));
    $this->s_post_collection = array("AddressGroupID" => 1, 
                                     "AddressGroupName" => "firm",
                                     "AddressGroupDescription" => "New Firm Description" );

    $this->s_put_collection = urlencode($this->s_post_collection);

    $this->m_delete_id = 1;

    $this->obj_addr_collection = new \api\address\AddressCollection();
  }

  public function testReadAddress()
  {
    $this->assertEquals($this->obj_addr_collection->read(array("AddressID" => 1)), true);
  }

  public function testCreateAddress()
  {
    $this->assertEquals();
  }

  public function testUpdateAddress()
  {
    $this->assertEquals();
  }

  public function testRemoveAddress()
  {
    $this->assertEquals();
  }
}

?>