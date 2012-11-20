<?php


switch($_SERVER['HTTP_HOST']){
		
	default:
		$dbhost = "localhost";
		$dbname = "wishlist";
		$dbuser = "wishlist";
		$dbpass = "wishlist";
	break;
}

// table prefixes if you need them.
$options['table_prefix'] = '';

//What algorithm to use to hashpasswords. 
// 'NoHash' for no password hasher
// 'MD5Hasher' for MD5
// More to come in the future.
$options['password_hasher'] = 'MD5Hasher';
 
//Relative path where to upload files to. Include a trailing slash. Make sure this path is writable by your server!
$options['filepath'] = 'uploads/'; 

//Where to upload files to. Make sure this path is writable by your server!
$options['currency_symbol'] = '$';

//Switch to large icons
$options['large-icons'] = true;

//Outputs error information where available to a log if true. Not fully implemented.
$options['logErrors'] = true; 
?>