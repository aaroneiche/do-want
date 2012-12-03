<?php
/*
This script handles ajax calls for the server.
*/

function __autoload($class_name) {
    require_once strtolower($class_name . '.class.php');
}

//We need the configuration
if(file_exists("config.php")){
	require_once("config.php");
}


/*
Previously, we were passing an empty array if args were not provided, by setting args to the $_REQUEST array, we can pass
information from defined forms or non-ajax made calls.
*/

if(!isset($_REQUEST['args'])){
	$_REQUEST['args'] = $_REQUEST;
}


//Create new instance of the 
$instance = new $_REQUEST['interact']();

$instance->dbhost = $dbhost;
$instance->dbname = $dbname;
$instance->dbuser = $dbuser;
$instance->dbpass = $dbpass;
$instance->options = $options;

/*
We provide a flag for non-db calls. Ideally anything not done with the DB is done client-side,
but for some setup items, we need to have server-side stuff happening.
*/
if(!isset($_REQUEST['nodb'])){
	$instance->dbConnect();
}

//encode and return whatever the class method returned.

print json_encode($instance->$_REQUEST['action']($_REQUEST['args']));

?>