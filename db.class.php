<?php

class db{

	public $dbname;
	public $dbuser;
	public $dbpass;
	public $dbhost;

	private $dbConn;
	public $debug;

	function dbConnect(){
		$this->dbConn = mysql_connect($this->dbhost,$this->dbuser,$this->dbpass);

		$select = mysql_select_db($this->dbname,$this->dbConn);	
		if(!$select) error_log("err ".mysql_error());
	}

	
	function dbQuery($query){
		$result = mysql_query($query,$this->dbConn);
		
		if($result !== false){
			return $result;
		}else{
			error_log(mysql_error()." ".$query);
		}
		
	}

	function dbValue($resource){
		return mysql_result($resource,0);
	}
	
	function dbAssoc($resource, $forceMulti = false){
		/*
		@resource: The Mysql Resource containing the data.
		@forceMulti: Forces the row data to be put in an array, regardless of row count.
		
		*/
		
		while($row = mysql_fetch_assoc($resource)){

			if(mysql_num_rows($resource) > 1 || $forceMulti == true){ //
				$assocData[] = $row;
			}else{
				$assocData = $row;
			}
		}
		
		return $assocData;
	}
	
	function dbLastInsertId(){
		return mysql_insert_id();
	}

	function dbRowCount($resource){
		return mysql_num_rows($resource);
	}

	//The escape function is built here so we can swap DB classes.
	function dbEscape($queryString){
		return mysql_real_escape_string($queryString);
	}

	function dbDisconnect(){
		mysql_connect($this->dbConn);
	}

	function __deconstruct(){
		$this->dbDisconnect();
	}

	/*
		Method sendMessage
		Sends a message from the current user to another user in the system.
		Because both user and wishlist classes need access to this method, it's being defined here.
		
		senderId - The id of the Sender
		receiverId - The id of the receiver
		message - The Text of the Message
		forceEmail - If this is true, email will be sent regardless of preference.
		
	*/
	function sendMessage($args){
		$cleanSenderId = $this->dbEscape($args['senderId']);
		$cleanReceiverId = $this->dbEscape($args['receiverId']);
		$cleanMessage = $this->dbEscape($args['message']);
		$cleanForceEmail = $this->dbEscape($args['forceEmail']);
		
		
		$emailQuery = "select {$this->options["table_prefix"]}users.*, (select {$this->options["table_prefix"]}users.fullname from {$this->options["table_prefix"]}users where {$this->options["table_prefix"]}users.userid = $cleanSenderId) as senderFullname from {$this->options["table_prefix"]}users where userid = $cleanReceiverId";
		
		/*
		//This should be a compatible table prefix version of the above query. Kept here for later.
		
		$emailQuery = "Select u.*, 
			(select u.fullname 
				from {$this->options["table_prefix"]}users as u 
				where u.userid = $cleanSenderId) 
			as senderFullname
		from 
			{$this->options["table_prefix"]}users as u
		where u.userid = $cleanReceiverId";
		*/
		
		$usersResult = $this->dbAssoc($this->dbQuery($emailQuery));

		$query = "insert into {$this->options["table_prefix"]}messages(sender,recipient,message,created) values($cleanSenderId,$cleanReceiverId,\"$cleanMessage\",NOW())";
		$result = $this->dbQuery($query);
		
		$subject = "New message from the Wishlist!";	
		
		if($usersResult['email_msgs'] == 1 || $cleanForceEmail == 1){
			$mailresult = mail($usersResult['email'],$subject,stripslashes($args['message']));
			return $mailresult;
		}
		return $result;
	}
	
	function markMessageRead($messageId){
		$query = "update {$this->options["table_prefix"]}messages set isread = 1 where messageid = $messageId";
		$result = $this->dbQuery($query);
		return $result;
	}
	
}





?>