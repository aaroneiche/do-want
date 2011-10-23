<?php
session_start();

class user extends db{
	
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
	
	function logoutUser(){
		//clear out the session and remove it.
		session_unset();
		session_destroy();
		
		return true;		
	}

/*
TODO:
addUser
deleteUser
editUser

*/

	

}

?>