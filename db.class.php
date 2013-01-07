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
		
		if($this->options['charSet'] != ''){
			$setChar = mysql_set_charset($this->options['charSet'],$this->dbConn);
		}
		
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


	/*
	Method manageEvent
	Adds, updates or deletes an event in the system
	
	eventaction - which action to take: add, edit, or delete
	eventid - (for edit or delete) The id of the event to manage.
	userid - The id of the user creating this event.
	date - The date of this event
	description - A description of this event
	recurring - whether or not this event
	
	*/

	function manageEvent($args){
		
		
		
	}


	/*
	Method checkForUpdates
	Checks with a defined remote server to determine if any updates are available.
	
	
	
	
	*/


	
	/*
	Method updateFiles
	Unpacks an update, checks it for validity, and processes an update.
	*/

	function updateFiles($filePath){
		
		include_once('config.php');
		$responseObject = array();
				
		//Create a new zipArchive Object for extracting the archive.
		$zip = new ZipArchive;
		$res = $zip->open($filePath);
	

		//If our archive opens properly.
		if ($res === TRUE) {
			//Find the manifest file
			$manifestFile = $zip->locateName("manifest.json");
		
			//If we can find the manifest file in the archive
			if($manifestFile){

				//Lets get that manifest out and look at it.
				$manifest = $zip->getFromIndex($manifestFile);
				$manifestArray = json_decode($manifest,true); //extract it to a php array.
			
				if($manifestArray['version'] > VERSION){
			
					$zip->extractTo("updates");
			
					//Iterate through each of the files in the file list. 
					foreach($manifestArray['filelist'] as $file){
				
						$calculatedSum = md5_file("updates/".$file['file']); // calculated the extracted file's checksum.
						$isMatch = $calculatedSum == $file['checksum']; //Check that the info in the manifest matches the actual file values.
						//$isMatchOutput == ($isMatch)?"Yes":"No"; //A helpful display value.

						if($isMatch){

							$copyResult = rename("updates/".$file['file'],$file['file']); //Move the file to it's appropriate location
							$copyResultChecksum = md5_file($file['file']); //Get the checksum of the file for the newly moved file.
						
							//Verify the file we just moved is the file we wanted to move.
							if($copyResultChecksum == $file['checksum']){ 
								//print "<p>".$file['file']." copied and verified</p>";
							}
						}				
					}

				}else{
					//versions aren't a good match
					print "Version in update file is older than you're running";
				}
			
			}else{
				//No manifest file, this is probably not a valid update file.
				print "No manifest file was found, this may not be a valid update archive.";
			}
			 $zip->close();
		 
	     } else {
	         echo 'failed';
	     }
		
		
		
		
		
		
	}



	
}

?>