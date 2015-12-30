<?php
require 'vendor/autoload.php';
require 'db_objects.php';

$instance = new Hybrid_Auth('authconf.php');

if($_GET['s'] == 'facebook'){
	$adapter = $instance->authenticate( "Facebook" );
}else if($_GET['s'] == 'google'){
	$adapter = $instance->authenticate( "Google" );
}

try{
	$user_profile = $adapter->getUserProfile();
}
catch( Exception $e){
    print "An error occurred: " . $e->getMessage();
    print " Error code: " . $e->getCode();	
}


//If we're creating a user.
if(isset($_GET['create'])){

	//Query to make sure we're not creating a duplicate account.
	$userCheck = Model::factory('Users')->where('email',$user_profile->email)->find_one();
	
	//If there's no match for the query, factory returns false;
	if($userCheck !== false){
		print "There appears to be an account with this address already created.
		<br/>Are you sure you don't have one? You might try loggin in with this service, or requesting your password";
		return;
	}

	//Create an instance.
	$user = Model::factory('Users')->create();
	$user->fullname = $user_profile->firstName . " " . $user_profile->lastName;
	
 	//Username will be first + last.
	$user->username = strtolower($user_profile->firstName . $user_profile->lastName);

	//If there's a verified email, we'll use that.
	$email = ($user_profile->emailVerified != "") ? $user_profile->emailVerified : $user_profile->email;
	$user->email = $email;

	$randomPassword =substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
	$user->password = $randomPassword;
	$user->save();

	//Mail an Admin to authorize this user.
	$admin = Model::factory('Users')->where('admin',1)->find_one();
	
	$message = $user->fullname ." has requested an account on your wishlist system. Please login and approve them.";
	$mailResult = mail($admin->email,"New user added to Wishlist",$message);

	//Add the auth for the provided social network for this user.
	//createSocialAuthForUser($user->getUserid(),$_GET['s'],$user_profile->identifier);
	createSocialAuthForUser($user->userid,$_GET['s'],$user_profile->identifier);
	print "Your account has been generated and is awaiting approval! <br/> You should have access shortly!";
	return;
}

//If we're adding social login for this user/
if(isset($_GET['add'])){
	
	$uid = (isset($_GET['uid'])) ? $_GET['uid'] : -1 ;
	//$user = UsersQuery::create()->filterByUserid($_GET['uid'])->findOne();
	$user = Model::factory("Users")->where("userid",$_GET['uid'])->findOne();

	if(count($user) > 0){
		//createSocialAuthForUser($user->getUserid(),$_GET['s'],$user_profile->identifier);
		createSocialAuthForUser($user->userid, $_GET['s'], $user_profile->identifier);
		print "Your Account login for {$_GET['s']} has been created";
	}else{
		print "We couldn't find your account. There may be a problem. Please contact your administrator.";
		return; 
	}
}

//If we're authenticating a user.
if(isset($_GET['auth'])){

	//Request the user in the Providers table.
	$socialMatch = Model::factory('UserAuthProviders')->where('social_id',$user_profile->identifier)->findOne();

	//If you find a matching user, login.
	if($socialMatch !== false){

		//$user = $socialMatch->getUsers();
		$user = $socialMatch->user()->findOne();
		if($user->approved == 1){
			loginUser($user->userid, $user->fullname, $user->admin);
			
			//Reload the parent window to the app, logged in. Then Close this window.
			?>
			<script>
				window.opener.location.reload();
				window.close();
			</script>";
			<?php
		}else{
			print "Your account has not been approved yet.";
		}
	}else{
		//No matching user found.
		print "We weren't able to find an account associated with this Social Media Account.
		<br/>Are you sure you've set one up?";
	}
}

function loginUser($userId, $fullname, $admin){
	$_SESSION['loggedIn'] = true;
	$_SESSION['userid'] = $userId;
	$_SESSION["fullname"] = $fullname;
	$_SESSION["admin"] = $admin;	
}

function createSocialAuthForUser($user_id,$provider,$social_uid){
	
	$userAuth = Model::factory('UserAuthProviders')->create();
	$userAuth->user_id = $user_id;
	$userAuth->provider = $provider;
	$userAuth->social_id = $social_uid;
	$userAuth->save();
}
