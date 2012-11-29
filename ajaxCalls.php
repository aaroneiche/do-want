<?php
/*
This script handles ajax calls for the server.
*/

function __autoload($class_name) {
    require_once strtolower($class_name . '.class.php');
}

//We need the configuration
require_once("config.php");

/*
Previously, we were passing an empty array if args were not provided, by setting args to the $_REQUEST array, we can pass
information from defined forms or non-ajax made calls.
*/

if(!isset($_REQUEST['args'])){
	$_REQUEST['args'] = $_REQUEST; //array();
}


//Create new instance of the 
$instance = new $_REQUEST['interact']();

$instance->dbhost = $dbhost;
$instance->dbname = $dbname;
$instance->dbuser = $dbuser;
$instance->dbpass = $dbpass;
$instance->options = $options;

$instance->dbConnect();

//encode and return whatever the class method returned.
//$jsonData =  json_encode($instance->$_REQUEST['action']($_REQUEST['args']));
//print $jsonData;
//if($options['logErrors']) error_log($jsonData);

print json_encode($instance->$_REQUEST['action']($_REQUEST['args']));

?>