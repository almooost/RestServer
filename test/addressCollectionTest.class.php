<?php
/**
 * Test addressCollection class
 * @author Sam Alfano (c) 2015
 * @date 20.06.15
 * @version v0.1
 * @group address
 */

class addressCollectionTest extends \PHPUnit_Framework_TestCase
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

    $this->s_put_collection = array("AddressGroupID" => 1, 
                                     "AddressGroupName" => "tsz",
                                     "AddressGroupDescription" => "New Firm Description" );

    $this->s_put_collection_url = urlencode($this->s_json_collection);

    $this->m_delete_id = 1;

    $this->obj_addr_collection = new \api\address\AddressCollection();

  }
  /**
   * Test if read is possible
   * @group address
   */
  public function testReadAddress()
  {
    $this->assertEquals(true, $this->obj_addr_collection->read("firm"));
  }

  public function testCreateAddress()
  {
    $this->assertEquals(false, $this->obj_addr_collection->create($this->s_post_collection));
    $this->assertEquals(true, $this->obj_addr_collection->create($this->s_put_collection));
  }

  public function testUpdateAddress()
  {
    $this->assertEquals(true, $this->obj_addr_collection->update($this->s_post_collection));
  }

  public function testRemoveAddress()
  {
    $this->assertEquals(true, $this->obj_addr_collection->remove("tsz"));
  }
}

?>