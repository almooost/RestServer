<?php

include_once('autoloader.php');
include_once '../config.php';
// Register the directory to your include files
$db = \db\dbController::getInstance($config['db']['db1']);

?>