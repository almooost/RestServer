<?php
/**
 * Main File in Project, is called every request
 * Handles all requests and calls the appropiate object for working
 * @author Sam Alfano
 * @date 13.06.15
 * @version v0.1
 */
include_once 'config.php';
/**
 * Provide autoloading and initialize most used objects
 */
include_once LIBRARY_PATH.'/autoload.lib.php';

$db      = \db\dbController::getInstance($config['db']['db1']);
$request = new \utility\requestController($_SERVER);
$auth    = new \api\auth\Auth($request);

if($request->isValid() && $auth->isAuth())
{
  if(preg_match('/^(\/address)/',$request->getPath("full")) )
  {
    $_POST['data'] = json_encode(array("AddressGroupID" => 1));
    if($request->getMethod() == "GET")
      $obj_address = api\address\Address::read($request);
    else if ($request->getMethod() == "PUT")
      $obj_address = api\address\Address::create(json_decode(file_get_contents("php://input"), true) );
    else if ($request->getMethod() == "POST")
      $obj_address = api\address\Address::update(json_decode($_POST['data'],true));
    else if ($request->getMethod() == "DELETE")
      $obj_address = api\address\Address::remove(json_decode(file_get_contents("php://input"), true));
  }
}
else
{
  $obj_response = new \utility\responseController(array("Error" => "Invalid request"), 400);

}




?>