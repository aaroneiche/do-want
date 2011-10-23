<?php


switch($_SERVER['HTTP_HOST']){
		
	default:
		$dbhost = "localhost";
		$dbname = "wishlist";
		$dbuser = "mywishlist";
		$dbpass = "password";
	break;
}

$options['table_prefix'] = '';
$options['password_hasher'] = 'MD5';
$options['filepath'] = 'uploads/';


?>