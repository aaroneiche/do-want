<?php

	include_once 'config.php';
	require_once 'db.class.php';

	$init = new db;
	
	$init->dbhost = $dbhost;
	$init->dbname = $dbname;
	$init->dbuser = $dbuser;
	$init->dbpass = $dbpass;
	$init->options = $_SESSION['options'];
	$conn = $init->dbConnect();
	
	$ops = $init->dbQuery("select * from options");
	
	foreach($init->dbAssoc($ops) as $option){
		$_SESSION['options'][$option['option_name']] = $option['option_value'];
	}

	define("VERSION",$_SESSION['options']['version']);
	define("USER_AGENT_STRING",$_SESSION['options']['user_agent_string']);
//	$options = $_SESSION['options'];

?>