<?php

	include_once 'config.php';
	require_once 'db.class.php';

	$init = new db;
	
	$init->dbhost = $dbhost;
	$init->dbname = $dbname;
	$init->dbuser = $dbuser;
	$init->dbpass = $dbpass;
	$init->options = $options;
	
	$init->dbConnect();
		
	$ops = $init->dbQuery("select * from options");
	
	$_SESSION['options'] = $init->dbAssoc($ops);

?>