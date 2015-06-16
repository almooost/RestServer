<?php
/**
 * Main Configuration file
 */
 
$config = array(
    "db" => array(
        "db1" => array(
            "database" => "api",
            "username" => "user",
            "password" => "password",
            "host" => "localhost"
        )
    ),
    "urls" => array(
        "baseUrl" => "http://api.local:8000"
    ),
    "paths" => array(
        "resources" => "/data",
        "images" => array(
            "content" => $_SERVER["DOCUMENT_ROOT"] . "/assets/images",
            "layout" => $_SERVER["DOCUMENT_ROOT"] . "/assets/images"
        )
    ),
    "api" => array(
        "paths" => array(
            "address" => "/address",
            "auth"    => "/auth"
        )
    )
);
 
/*
 Include Paths for classes and libraries
*/
define("LIBRARY_PATH", $_SERVER["DOCUMENT_ROOT"].'/inc/lib');
define("CLASS_PATH", $_SERVER["DOCUMENT_ROOT"].'/inc/class');
 
/*
 Error reporting.
*/
ini_set("error_reporting", "true");
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL|E_STRCT);


 
?>