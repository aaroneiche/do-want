<?php
/*
This script handles ajax calls for the server.
*/
function __autoload($class_name) {
    require_once strtolower($class_name . '.class.php');
}

//We need the configuration
require_once("config.php");

if(!isset($_REQUEST['args'])){
	$_REQUEST['args'] = array();
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
print json_encode($instance->$_REQUEST['action']($_REQUEST['args']));


?>