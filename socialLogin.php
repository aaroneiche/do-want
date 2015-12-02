<?php
require 'vendor/autoload.php';
require 'propel_conf.php';


$instance = new Hybrid_Auth('authconf.php');

if($_GET['s'] == 'facebook'){
	$adapter = $instance->authenticate( "Facebook" );
}else if($_GET['s'] == 'google'){
	$adapter = $instance->authenticate( "Google" );
}

$user_profile = $adapter->getUserProfile();


//If we're creating a user.
if(isset($_GET['create'])){

	//Query to make sure we're not creating a duplicate account.
	$userCheck = UsersQuery::create()->filterByEmail($user_profile->email)->findOne();
	if(count($userCheck) > 0){
		print "There appears to be an account with this address already created.
		<br/>Are you sure you don't have one? You might try loggin in with this service, or requesting your password";
		return;
	}

	$user = new Users();
	$user->setFullname($user_profile->firstName . " " . $user_profile->lastName);
		
	//Username will be first +last.
	$user->setUsername(strtolower($user_profile->firstName . $user_profile->lastName));

	//If there's a Verified email, we want to use that.	
	$email = ($user_profile->emailVerified != "") ? $user_profile->emailVerified : $user_profile->email;
	$user->setEmail($email);

	//If we're creating an account for someone via an Oauth provider, we want to generate a random password.
	$randomPassword =substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
	$user->setPassword($randomPassword);
	$user->save();

	//Mail an Admin to authorize this user.
	$admin = UsersQuery::create()->filterByAdmin(1)->findOne();
	
		$message = $user->getFullname() ."has requested an account on your wishlist system. Please login and approve them.";
		$mailResult = mail($admin->getEmail(),"New user added to Wishlist",$message);

	//Add the auth for the provided social network for this user.
	createSocialAuthForUser($user->getUserid(),$_GET['s'],$user_profile->identifier);
	print "Your account has been generated and is awaiting approval! <br/> You should have access shortly!";
	return;

}

//If we're adding social login for this user/
if(isset($_GET['add'])){
	
	$user = UsersQuery::create()->filterByEmail($user_profile->email)->findOne();

	if(count($userCheck) > 0){
		createSocialAuthForUser($user->getUserid(),$_GET['s'],$user_profile->identifier);
		print "Your Account login for {$_GET['s']} has been created";
	}else{
		print "An account for the social media account listed email address doesn't seem to exist. Please create an account with the social login instead.";
		return; 
	}
}

//If we're authenticating a user.
if(isset($_GET['auth'])){

	//Request the user in the Providers table.
	$socialMatch = UserAuthProvidersQuery::create()
		->filterBySocialId($user_profile->identifier)
		->findOne();

	//If you find a matching user, login.
	if(count($socialMatch) == 1){

		$user = $socialMatch->getUsers();
		
		if($user->getApproved() == 1){
			loginUser($user->getUserId(),$user->getFullname(),$user->getAdmin());

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
	$userAuth = new UserAuthProviders();
	$userAuth->setUserId($user_id);
	$userAuth->setProvider($provider);
	$userAuth->setSocialId($social_uid);
	$userAuth->save();
}
