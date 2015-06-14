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
    $obj_address = new api\address\Address($request);
  }
  //echo $request->format("json");
  //error_log(json_encode($request,JSON_PRETTY_PRINT));
  //error_log(json_encode(serialize($request)));
}
else
{
  $obj_response = new \utility\responseController(array("Error" => "Invalid request"), 400);

}




?>