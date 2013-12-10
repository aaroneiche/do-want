<?php
//Do want Default config file. Do not delete this - it's necessary for the configuration script. xxx

$dbhost = 'localhost';
$dbname = 'wishlist';
$dbuser = 'wishlist';
$dbpass = 'wishlist';

// table prefixes if you need them.
$options['table_prefix'] = '';

//What algorithm to use to hashpasswords. 
// 'NoHash' for no password hasher
// 'MD5Hasher' for MD5
// More to come in the future.
$options['password_hasher'] = 'MD5Hasher';
 
//Relative path where to upload files to. Include a trailing slash. Make sure this path is writable by your server!
$options['filepath'] = 'uploads/'; 

//The currency symbol you would like to show up in the wishlist system.
$options['currency_symbol'] = '$';

//Switch to large icons
$options['large-icons'] = true; 

//Outputs error information where available to a log if true. Not fully implemented.
$options['logErrors'] = false; 

//Enables custom inclusions in the custom directories.
$options['includeCustom'] = false;

//Character encoding for MySQL - If you're having problems with info coming out of the data. Change this to '', or change to 'utf8'
//Alternatively, you can set any character set you like here.
$options['charSet'] = 'utf8';

?>