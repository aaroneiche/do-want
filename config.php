<?php


switch($_SERVER['HTTP_HOST']){
		
	default:
		$dbhost = "localhost";
		$dbname = "dowant";
		$dbuser = "mywishlist";
		$dbpass = "password";
	break;
}

$options['table_prefix'] = ''; // table prefixes if you need them.
$options['password_hasher'] = 'MD5'; //What algorithm to use to hashpasswords. 
$options['filepath'] = 'uploads/'; //Where to upload files to


?>