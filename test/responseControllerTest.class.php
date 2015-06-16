<?php
/**
 * Test Class for responseController
 */
include_once 'config.php';
include_once 'lib/autoload.lib.php';
include_once 'inc/class/utility/responsecontroller.class.php';


class reponseControllerTest extends PHPUnit_Framework_TestCase
{

  public function testdefaultInitialization()
  {
    $obj = new \utility\responseController(array("Address" => "TestAddress"));

  }
}

?>