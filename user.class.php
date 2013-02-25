<?php
if (session_id() == "") session_start();

class user extends db{
	
/*
	Method: loginUser
	Logs a user in by setting $_SESSION variables appropriate to the session.
	
	@username - The username of the user logging in.
	@password - the password of the user logging in.
*/	
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
	
/*
	Method: logoutuser
	Logs out the current user by destroying their session.
	
*/	
	function logoutUser(){
		//clear out the session and remove it.
		session_unset();
		session_destroy();
		
		return true;		
	}



/*
	Method MD5Hasher
	Returns an MD5 hash of a string
*/
	function MD5Hasher($stringToHash){
		return md5($stringToHash);
	}

/*
	Method NoHash
	A hack solution for no password hashing (not recommended for security's sake)
*/
	function NoHash($stringToHash){
		return $stringToHash;
	}

/*
	Method: getShopForUsers
	Gets a list of users that the current logged-on user may shop for uses the $_SESSION['userid'] variable.
	
*/
	function getShopForUsers(){
		$query = "select users.fullname, users.userid, shoppers.pending from users, shoppers where shoppers.mayShopFor = users.userid and shoppers.shopper = {$_SESSION['userid']}";
		$result = $this->dbQuery($query);
		return $this->dbAssoc($result);
	}

/*
	Method: getUsersShoppingFor
	Gets a list of users are shopping for current user.
	
*/
	function getUsersShoppingFor(){
		$query = "select users.fullname, users.userid, shoppers.pending from users, shoppers where shoppers.mayShopFor = {$_SESSION['userid']} and shoppers.shopper = users.userid ";
		$result = $this->dbQuery($query);
		return $this->dbAssoc($result);
	}


/*
	Method: requestShopForUser
	Requests that a user may shop for another user - we make the assumption that users who already have requests in will not be able to call this.
	
	@shopperId - The user wishing to shop
	@shopForUserId - The user whom they would like to shop for.
*/
	function requestShopForUser($args){
		$query = "insert into {$this->options["table_prefix"]}shoppers(shopper,mayshopfor,pending) values({$args['shopperId']},{$args['shopForUserId']},1)";
		$result = $this->dbQuery($query);
		
		$name = $this->dbValue($this->dbQuery("select {$this->options["table_prefix"]}users.fullname from {$this->options["table_prefix"]}users where {$this->options["table_prefix"]}users.userid = '{$args['shopperId']}'"));
		
		if($result){
			$message = "$name has requested to be added to your list of people who may shop for you. Please log into Do Want to approve them.";
			
			$messageData = array(
							'senderId'=>0,
							'receiverId'=> $args['shopForUserId'],
							'message'=>$message);
							
			$this->sendMessage($messageData);
		}
		
		return $result;
	}


/*
	Method: removeShopForUser
	Removes a user being shopped for from the current user's list.

	@shopperId - The user wishing to shop
	@shopForUserId - The user whom they would like to shop for.	
*/
	function removeShopForUser($args){
	//	$query = "insert into shoppers(shopper,mayshopfor,pending) values({$args['shopperId']},{$args['shopForUserId']},1)";
		if($_SESSION['userid'] == $args['shopperId'] || $_SESSION['admin'] == true){
			$query = "delete from shoppers where shopper = {$args['shopperId']} and mayShopFor = {$args['shopForUserId']}";
			$result = $this->dbQuery($query);
			return $result;
		}else{
			return array('type'=>'error','message'=>"You cannot remove Shoppers for other users' lists.");
		}
	}


/*
	Method: approveShopForUser
	Set a shopper approved for a particular user.

	@shopForId - The user approving the request
	@shopperId - The user who wanted to shop for userId
*/
	function approveShopForUser($args){
		
		if($_SESSION['userid'] == $args['shopForId'] || $_SESSION['admin'] == true){
			$query = "update shoppers set pending = 0 where shopper={$args['shopperId']} and mayShopFor = {$args['shopForId']}";
			$result = $this->dbQuery($query);
			return $result;
		}else{
			return array('type'=>'error','message'=>"You cannot approve Shoppers for other users.");
		}
	}

/*
	Method: disapproveShopForUser
	Remove an approved shopper for a particular user - does not currently eliminate purchases by this user.

	@shopForId - The user approving the request
	@shopperId - The user who wanted to shop for userId
*/
	function disapproveShopForUser($args){
		//If this user is the owner of his account, or is admin...
		if($_SESSION['userid'] == $args['shopForId'] || $_SESSION['admin'] == true){
			$query = "delete from shoppers where shopper={$args['shopperId']} and mayShopFor = {$args['shopForId']}";
			$result = $this->dbQuery($query);
			return $result;
		}else{
			return array('type'=>'error','message'=>"You cannot remove Shoppers for other users.");
		}
	}


/*
	Method: getUser
	Returns basic information about a user. Logged on user must be that user or an administrator. 
	@userid - The ID of the user you want the information for.
*/
	function getUser($args){
		if($_SESSION['userid'] == $args['userid'] || $_SESSION['admin'] == 1){
			$cleanUserId = $this->dbEscape($args["userid"]);
			$query = "select username, fullname, userid, email, email_msgs, admin, approved from users where userid = '{$cleanUserId}'";
		
			$result = $this->dbQuery($query);
			return $this->dbAssoc($result);
		}else{
			//error_log("User information was requested for UserId ".$args['userid']." by a non-admin, non-self user");
			return array("message"=>"You do not have permission to get that information");
		}
	}
	
	
/*
	Method: manageUser
	Takes a set of Arguements and adds, edits, or deletes a user.
	
	@userAction - The action to perform: add, edit, delete.
	@userid - The id of the user to edit or delete. If Empty, this will add a user.
	@username - The desired username
	@password - The desired password
	@fullname - The Full (real) name of the user
	@email - Email address
	@approved - Whether or not this user is approved to use the system.
	@admin - Whether or not this user is an admin.
*/	
	function manageUser($args){
		
		$cleanAction = $this->dbEscape($args['userAction']);
		$cleanUserId = $this->dbEscape($args['userId']);
		$cleanUsername = $this->dbEscape($args['username']);
		$cleanPassword = $this->dbEscape($args['password']);
		$cleanFullname = $this->dbEscape($args['fullname']);
		$cleanEmail = $this->dbEscape($args['email']);
		$cleanEmailMsg = $this->dbEscape($args['emailMsg']);
		$cleanApproved = ($this->dbEscape($args['approved']) == 1)?1:0; 
		$cleanAdmin = ($this->dbEscape($args['admin']) == 1)?1:0;
		
		$checkUniqueQuery = "select userid from users where email = '$cleanEmail' or username = '$cleanUsername'";
		$uniqueResult = $this->dbRowCount($this->dbQuery($checkUniqueQuery));
		
		$returnArray = array();
				
		switch($cleanAction){
			case 'add':
				if($uniqueResult >0){
					return array('type'=>"error",'message'=>'This email or username is already taken. Please choose another');
				}
			
				$hashedPassword = call_user_func_array(array($this,$this->options["password_hasher"]), array($cleanPassword));
				
				$query = "INSERT into {$this->options["table_prefix"]}users(username,password,fullname,email,email_msgs,approved,admin) VALUES('$cleanUsername','$hashedPassword','$cleanFullname','$cleanEmail','$cleanEmailMsg',$cleanApproved,$cleanAdmin)";

				
				$adminId = $this->dbAssoc($this->dbQuery("select * from {$this->options["table_prefix"]}users where admin = 1"),true);
								
				$sendRequest = $this->sendMessage(array("senderId"=>0,
										"receiverId"=>$adminId[0]['userid'],
										"message"=>"$cleanFullname has requested an account on your wishlist system. Please login and approve them",
										"forceEmail"=>false));
										
				$returnArray['sendRequest'] = $sendRequest;
				
				if($sendRequest == true){
					$returnArray['type'] = "success";
					$returnArray['message'] = "Your request has been sent to the administrator";
				}else{
					$returnArray['type'] = "error";
					$returnArray['message'] = "There was a problem sending your request. You may need to contact the administrator directly.";
				}
				
				$returnArray['result'] = $this->dbQuery($query);
				
			break;
			case 'edit':
		
				if(strlen($cleanPassword) > 0){
					$hashedPassword = call_user_func_array(array($this,$this->options["password_hasher"]), array($cleanPassword));
				
					$passwordSQL = ", password='$hashedPassword'";
				}else{
					$passwordSQL = "";
				}
				
				$approved = (isset($args['approved']))?", approved={$cleanApproved}":"";
				$admin = (isset($args['admin']))?", admin={$cleanAdmin}":"";
				
				$query = "UPDATE {$this->options["table_prefix"]}users SET username='{$cleanUsername}', fullname='{$cleanFullname}', email='{$cleanEmail}', email_msgs='{$cleanEmailMsg}' $passwordSQL $approved $admin WHERE userid ='{$cleanUserId}'";
				
				$returnArray['result'] = $this->dbQuery($query);				
			break;
			case 'delete':
				$query = "DELETE FROM {$this->options["table_prefix"]}users WHERE userid = '{$cleanUserId}'";
				$returnArray['result'] = $this->dbQuery($query);
			break;
		}
		
		return $returnArray;
	}
	

/*
	Method: resetUserPassword
	Randomizes and sets a new password for a user, returns temporary password.
	
	@userid - The id of the user who's password should be reset.
*/
	function resetUserPassword($args){

		$userid = $this->dbEscape($args['userid']);

		//we have to dbEscape this value to match the same input as will come from login.
		$randomPass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
		$escapedPass = $this->dbEscape($randomPass);

		//Gives us the same hash as will happen on login.
		$hashedResult = call_user_func_array(array($this,$this->options["password_hasher"]), array($randomPass));
		
		$query = "update {$this->options["table_prefix"]}users set password = '$hashedResult' where userid = $userid";
		error_log($query);
		
		$result = $this->dbQuery($query);
		if($result){
			return $escapedPass;
		}else{
			return false;
		}

	}

/*
	Method: requestPasswordReset
	Takes an email address and either resets the user password and sends an email, or returns unfound email error.
	
	@emailAddress - The email address of the account to reset.
	
*/
	function requestPasswordReset($args){
		$response = array();
		
		$cleanEmail = $this->dbEscape($args['emailAddress']);
		
		$query = "select userid, email from {$this->options["table_prefix"]}users where email = '$cleanEmail'";
		$result = $this->dbQuery($query);
		
		if($this->dbRowCount($result) > 0){
			$user = $this->dbAssoc($result);
			$newPass = $this->resetUserPassword(array('userid'=>$user['userid']));
			
			if($newPass != false){
				$msgArgs = array(
					'senderId' => 0,
					'receiverId' =>$user['userid'],
					'message'=> "A request has been made to reset your account's password.\nThe temporary password is:\n\n".$newPass."\n\n Please login to the wishlist site, login, and create a new password.",
 					'forceEmail' => true
				);
				
				$msg = $this->sendMessage($msgArgs);
				if($msg){
					$response['responseType'] = 'success';
					$response['message'] = 'Your password has been reset and sent to your email address.';
				}else{
					$response['responseType'] = 'error';
					$response['message'] = "We had trouble sending the message. Please contact the system administrator";
				}
				error_log($msg);
				
			}else{
				$response['responseType'] = 'error';
				$response['message'] = "There was a problem . Please contact the system administrator";
			}
			return $response;
		}else{
			$response['responseType'] = 'error';
			$response['message'] = "We could not find a user account with that email address. Check for typos or contact the system administrator.";
			return $response;
		}
	}


/*
	Method: getListOfUsers
	Returns a list of all users in the system. - Takes no arguments.
	
*/
	function getListOfUsers(){
		$query = "select userid, username, fullname, email from users";
	
		$result = $this->dbQuery($query);
		return $this->dbAssoc($result,true);
	}

	/*
		Method: getListOfNonShopUsers
		Returns a list of users for whom the current user is not approved. 

	*//*
		function getListOfUsers($args){
			$args['shopperId'];
			
			$query = "select userid, username, fullname, email from users";

			$result = $this->dbQuery($query);
			return $this->dbAssoc($result);
		}
		*/

/*
	Method: getMessages
	Returns an array of user's messages

	@userid - The id of the user whom you want to get messages for
	@readStatus - messages that have been read or not: 
		0 = not read
		1 = read
		2 = all

*/
		function getMessages($args){
			$readStatusQuery = ($args['readStatus'] == 2)? "":" and isread = ".$args['readStatus'];
			$query = "select m.*, u.fullname from {$options['table-prefix']}messages m left join {$options['table-prefix']}users u on u.userid = m.sender where m.recipient = {$args['userid']} ".$readStatusQuery;
			

			$result = $this->dbQuery($query);
			return $this->dbAssoc($result,true);
		}


/*
	Method: markMessageRead
		Sets the "isRead" flag in the table as read.
		@messageId - The message to be marked read.
*/
	function markMessageRead($args){
		$query = "update messages set isread = 1 where messageid = {$args['messageId']}";
		$result = $this->dbQuery($query);

	 	return $this->dbAssoc($result);
	}
	
}
?>
