<?php
/**
 * Test addressCollection class
 * @date 20.06.15
 * @version v0.1
 * @group address
 */

class addressCollectionTest extends \PHPUnit_Framework_TestCase
{  

  protected $s_post_collection;
  protected $s_put_collection;
  protected $m_delete_id;

  protected $obj_addr_collection;

  protected function setUp()
  {
    $this->s_post_collection = array("AddressGroupID" => 1, 
                                     "AddressGroupName" => "firm",
                                     "AddressGroupDescription" => "New Firm Description" );

    $this->s_put_collection = array("AddressGroupID" => 1, 
                                     "AddressGroupName" => "tsz5",
                                     "AddressGroupDescription" => "New Firm Description" );

  }
  /**
   * Test if read is possible
   */
  public function testReadAddress()
  {
    $this->obj_addr_collection = new \api\address\AddressCollection();
    $this->assertEquals(true, $this->obj_addr_collection->read("firm"));
  }

  public function testCreateAddress()
  {
    $this->obj_addr_collection = new \api\address\AddressCollection($this->s_post_collection);
    $this->assertEquals(false, $this->obj_addr_collection->create());

    $this->obj_addr_collection = new \api\address\AddressCollection($this->s_put_collection);
    $result = $this->obj_addr_collection->create();
    $this->assertEquals(1, $result['status']);
    return $result['data'];
  }

  public function testUpdateAddress()
  {
    $this->obj_addr_collection = new \api\address\AddressCollection($this->s_put_collection);
    $this->assertEquals(true, $this->obj_addr_collection->update());
  }

  /**
   * @depends testCreateAddress
   */
  public function testRemoveAddress($i_delete_id)
  {
    $h_delete_id = array('AddressGroupID' => $i_delete_id);
    $this->obj_addr_collection = new \api\address\AddressCollection();
    $this->assertEquals(true, $this->obj_addr_collection->remove($h_delete_id));
  }
}

?>