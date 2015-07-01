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
  //var_dump($request);
  if(preg_match('/^(\/address)/',$request->getPath("full")) )
  {
    if(preg_match('/^(\/address\/group\/)/', $request->getPath("full")))
    {
      if($request->getMethod() == "GET")
      {
        $obj_address = new \api\address\AddressCollection();
        $obj_address->read($request->getPath());
      }
      else if ($request->getMethod() == "PUT")
      {
        $obj_address = new \api\address\AddressCollection($request->getPayload() );
        if($obj_address->create())
          $obj_address->finalize(200);

      }
      else if ($request->getMethod() == "POST")
      {
        $obj_address = new \api\address\AddressCollection($request->getPayload());
          if($obj_address->update())
            $obj_address->finalize(200);
      }
      else if ($request->getMethod() == "DELETE")
      {
        $obj_address = new \api\address\AddressCollection();
        if($obj_address->remove($request->getPayload()))
          $obj_address->finalize(200);
      } 
    }
    else
    {
      if($request->getMethod() == "GET")
      {
        $obj_address = new \api\address\AddressEntity();
        if($obj_address->read($request->getPath()))
          $obj_address->finalize(200);
      }
      else if ($request->getMethod() == "PUT")
      {
        $obj_address = new \api\address\AddressEntity($request->getPayload() );
        if($obj_address->create())
          $obj_address->finalize(200);
      }
      else if ($request->getMethod() == "POST")
      {
        $obj_address = new \api\address\AddressEntity($request->getPayload());
        if($obj_address->update())
          $obj_address->finalize(200);
      }
      else if ($request->getMethod() == "DELETE")
      {
        $obj_address = new \api\address\AddressEntity();
        if($obj_address->remove($request->getPayload()))
          $obj_address->finalize(200);
      }
    }
  }
  $obj_response = new \utility\responseController(array("Error" => "Invalid request"), 400);
}
else
{
  $obj_response = new \utility\responseController(array("Error" => "Invalid request"), 400);

}




?>