<?php


switch($_SERVER['HTTP_HOST']){
		
	default:
		$dbhost = "localhost";
		$dbname = "wishlist";
		$dbuser = "wishlist";
		$dbpass = "wishlist";
	break;
}

$options['table_prefix'] = ''; // table prefixes if you need them.
$options['password_hasher'] = 'MD5'; //What algorithm to use to hashpasswords. 
$options['filepath'] = 'uploads/'; //Where to upload files to
$options['logErrors'] = true; //Outputs error information where available to a log if true.

?>