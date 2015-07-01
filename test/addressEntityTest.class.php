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
  protected $s_search_value;
  protected $s_search_attr;
  protected $s_json_entity;
  protected $s_post_entity;
  protected $s_put_entity;
  protected $m_delete_id;

  protected $obj_addr_entity;

  protected function setUp()
  {
    $this->s_search_value = "Sam";
    $this->s_search_attr  = "Firstname";

    $this->s_post_entity = array("AddressID" => '4',
                                "AddressGroupID" => 1,
                                "Title" => "Herr",
                                "Firstname" => "Sam",
                                "Lastname" => "Alfano",
                                "Street" => "Oberseestr.59",
                                "ZIP" => "8640",
                                "Location" => "Rapperswil",
                                "Country" => "Switzerland",
                                "Phone" => "079 708 84 68",
                                "EMail" => "alfano.samuel@gmail.com",
                                "Status" => 1);

    $this->s_put_entity = array("AddressID" => 'NULL',
                                "AddressGroupID" => 2,
                                "Title" => "Herr",
                                "Firstname" => "Dominik",
                                "Lastname" => "Altermatt",
                                "Street" => "Feldweg 10",
                                "ZIP" => "8200",
                                "Location" => "Uster",
                                "Country" => "Switzerland",
                                "Phone" => "079 548 68 12",
                                "EMail" => "altermatt.dominik@gmail.com",
                                "Status" => 1);

    $this->m_delete_id = 4;

  }

  public function testReadAddress()
  {
    $this->obj_addr_entity = new \api\address\AddressEntity();
    //$this->assertContainsOnlyInstancesOf('\api\address\AddressEntity', $this->obj_addr_entity->read($this->s_search_value, $this->s_search_attr));
  }

  public function testCreateAddress()
  {
    $this->obj_addr_entity = new \api\address\AddressEntity($this->s_post_entity);
    $result = $this->obj_addr_entity->create();
    $this->assertEquals(1, $result['status']);

    $this->obj_addr_entity = new \api\address\AddressEntity($this->s_put_entity);
    $result = $this->obj_addr_entity->create();
    $this->assertEquals(1, $result['status']);
    return $result['data'];
  }

  public function testUpdateAddress()
  {
    $this->obj_addr_entity = new \api\address\AddressEntity($this->s_post_entity);
    $this->assertEquals(true, $this->obj_addr_entity->update());
  }

  /**
   * @depends testCreateAddress
   */
  public function testRemoveAddress($i_delete_id)
  {
    $h_delete_id = array('AddressID' => $i_delete_id);
    $this->obj_addr_entity = new \api\address\AddressEntity();
    $this->assertEquals(true, $this->obj_addr_entity->remove($h_delete_id));
  }

}

?>