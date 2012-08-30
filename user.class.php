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
		
		$query = "SELECT userid, fullname, admin FROM {$this->options["table_prefix"]}users WHERE username = '$cleanUsername' AND password = {$this->options["password_hasher"]}('$cleanPassword') AND approved = 1";
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
	Method: getShopForUsers
	Gets a list of users that the current logged-on user may shop for uses the $_SESSION['userid'] variable.
	
*/
	function getShopForUsers(){
		$query = "select fullname,userid from users, shoppers where shoppers.mayShopFor = users.userid and shoppers.shopper = {$_SESSION['userid']}";

		$result = $this->dbQuery($query);
		return $this->dbAssoc($result);
	}


/*
	Method: getUser
	Returns basic information about a user. Logged on user must be that user or an administrator. 
	@userid - The ID of the user you want the information for.
*/
	function getUser($args){
		if($_SESSION['userid'] == $args['userid'] || $_SESSION['admin'] == 1){
			$cleanUserId = $this->dbEscape($args["userid"]);
			$query = "select username, fullname, userid, email, admin, approved from users where userid = '{$cleanUserId}'";
		
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
		$cleanApproved = ($this->dbEscape($args['approved']) == 1)?1:0; //$this->dbEscape($args['approved']);
		$cleanAdmin = ($this->dbEscape($args['admin']) == 1)?1:0;
		
		switch($cleanAction){
			case 'add':
				$query = "INSERT into {$this->options["table_prefix"]}users(username,password,fullname,email,approved,admin) VALUES('$cleanUsername',{$this->options["password_hasher"]}('$cleanPassword'),'$cleanFullname','$cleanEmail',$cleanApproved,$cleanAdmin)";			
			break;
			case 'edit':
		
				if(strlen($cleanPassword) > 0){
					//Now we know we have a password to update.
					//We're going to want to move this to an external function from SQL.
					$passwordSQL = "password={$this->options["password_hasher"]}('$cleanPassword'), ";
				}else{
					$passwordSQL = "";
				}

				$query = "UPDATE {$this->options["table_prefix"]}users SET username='{$cleanUsername}', fullname='{$cleanFullname}', email='{$cleanEmail}', $passwordSQL
							approved={$cleanApproved}, admin={$cleanAdmin} WHERE userid ='{$cleanUserId}'";			
			break;
			case 'delete':
				$query = "DELETE FROM {$this->options["table_prefix"]}users WHERE userid = '{$cleanUserId}'";
			break;
		}
		
		
		$result = $this->dbQuery($query);
		return $result;
	}
	

	

/*
	Method: getListOfUsers
	Returns a list of all users in the system. - Takes no arguments.
	
*/
	function getListOfUsers(){
		if($_SESSION['admin'] == 1){
			$query = "select userid, username, fullname, email from users";
			
			$result = $this->dbQuery($query);
			return $this->dbAssoc($result);
		}
	}
	
		
}





?>