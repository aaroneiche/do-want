<?php
require_once("config.php");
require_once("vendor/autoload.php");

$dns = "mysql:host={$dbhost};dbname={$dbname}";

ORM::configure($dns);
ORM::configure('username', $dbuser);
ORM::configure('password', $dbpass);

//Users object
class Users extends Model {
	public static $_id_column = 'userid';

	function UserAuthProviders(){
		return $this->hasMany('UserAuthProviders','user_id');
	}

	function Items(){
		return $this->hasMany('Items');
	}
}

//UserAuthProviders object
class UserAuthProviders extends Model {

	function User() {
		return $this->belongs_to("Users","user_id");
	}
}

class Items extends Model {
	public function User() {
		return $this->belongs_to("Users");
	}
}

?>
