<?php
require 'vendor/autoload.php';
require "db.class.php";
require "user.class.php";
require "config.php";


$instance = new Hybrid_Auth('authconf.php');

if($_GET['s'] == 'facebook'){
	$adapter = $instance->authenticate( "Facebook" );
}else if($_GET['s'] == 'google'){
	$adapter = $instance->authenticate( "Google" );
}

$user_profile = $adapter->getUserProfile();


//Get the User Object
$userObject = new user();

$userObject->dbhost = $dbhost;
$userObject->dbname = $dbname;
$userObject->dbuser = $dbuser;
$userObject->dbpass = $dbpass;
$userObject->options = $options;

$dbC = $userObject->dbConnect();

$socialLoginQuery = "SELECT * FROM {$user->options["table_prefix"]}user_auth_providers WHERE social_id = '{$user_profile->identifier}'";
$socialUser = $userObject->dbQuery($socialLoginQuery);

if($userObject->dbRowCount($socialUser) > 0){

	$socialInfo = $userObject->dbAssoc($socialUser);
	$userInfoResult = $userObject->dbQuery("select * from {$user->options["table_prefix"]}users where userid = '{$socialInfo['user_id']}'");
	$userInfo = $userObject->dbAssoc($userInfoResult);

	$_SESSION['loggedIn'] = true;
	$_SESSION['userid'] = $userInfo['userid'];
	$_SESSION["fullname"] = $userInfo["fullname"];
	$_SESSION["admin"] = $userInfo["admin"];

?>
	Hang on, we're logging you in.
	<script>
		window.opener.location.reload();
		window.close();
	</script>

<?php

}else{
	print "We weren't able to find an account associated with this Social Media Account.
	<br/>Are you sure you've set one up?";
}


/*
$user_profile->verifiedEmail;

$userObject = new user();

if(strlen($user_profile->emailVerified) > 0){
	$userObject->dbConnect();

	$q = "SELECT userid, fullname, admin FROM {$user->options["table_prefix"]}users WHERE email = '{$user_profile->emailVerified}' and approved = 1";	
	print($q);
	$foundUser = $userObject->dbQuery($q);

	print "Proceed to login user";

}else{
	print "No matching email";
}
*/

/*
function loginUser(){
	
	$cleanUsername = $this->dbEscape($_REQUEST['username']); 
	$cleanPassword = $this->dbEscape($_REQUEST['password']);

	$hashedResult = call_user_func_array(array($this,$this->options["password_hasher"]), array($cleanPassword));

	$query = "SELECT userid, fullname, admin FROM {$this->options["table_prefix"]}users WHERE username = '$cleanUsername' AND password = '".$hashedResult."' AND approved = 1";
	$userInfoResult = $this->dbQuery($query);
	
	if($this->dbRowCount($userInfoResult) > 0){
		$userInfo = $this->dbAssoc($userInfoResult);
		
		$_SESSION['loggedIn'] = true;
		$_SESSION['userid'] = $userInfo['userid'];
		$_SESSION["fullname"] = $userInfo["fullname"];
		$_SESSION["admin"] = $userInfo["admin"];
		
		return true;
	}else{
		return false;
	}
}
*/