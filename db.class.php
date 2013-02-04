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
	Method: checkForUpdates
	Checks with a defined remote server to determine if any updates are available.	
	*/

	function checkForUpdates(){
		
		if (function_exists('curl_init')) {
			
		   $ch = curl_init(); 
		   curl_setopt($ch, CURLOPT_URL, $this->options['updateSource']."?v=".VERSION);
		   curl_setopt($ch, CURLOPT_HEADER, 0);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT_STRING);

		   $content = curl_exec($ch);
		   curl_close($ch);
		   
		   return json_decode($content);
		}
	}

	/*
	Method: updateFiles
	Unpacks an update, checks it for validity, and processes an update.
	Returns a list of updated files.
	
	@fileArray - The manifest file data (with filenames and relevant MD5 checksums)
	@zipArchive - The zipArchive object created with the update archive.
	
	*/
	function updateFiles($fileArray,$zipArchive){

		$responseObject = array(
			"updateList"
		);

		foreach($fileArray as $file){
	
			if(!isset($file['name'])){
				copyUpdateFiles($file,$zipArchive);
			}else{
				$fileChecksum = md5_file($file['path'].$file['name']);
				
				if($fileChecksum != $file['checksum']){
									
					$result = $zipArchive->extractTo($file['path'], $file['name']);
					
					if($result){
						$responseObject['updateList'][$file['name']] = "Updated!";
					}else{
						$responseObject['updateList'][$file['name']] = "Update failed";
					}					
					
				}				
			}
		}
		
		return $responseObject;
	}	
	

	/*
	Method: downloadUpdateFile
	Downloads a file to the defined uploads directory and returns the path when complete. 
	
	@fileUri - The URI of the file to download.
	@fileName - The Name of the file.
	
	*/
	function downloadUpdateFile($args){
		if (function_exists('curl_init')) {

		   $ch = curl_init(); 
		   curl_setopt($ch, CURLOPT_URL, $args['fileUri']);
		   curl_setopt($ch, CURLOPT_HEADER, 0);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT_STRING);

		   $content = curl_exec($ch);
		   curl_close($ch);
		   file_put_contents($this->options['filepath'].$args['fileName'], $content);
		   return $this->options['filepath'].$args['fileName'];
		}		
	}


	/*
	Method: backupApp
	This method builds a file-copy backup of the Do Want application. It adds items into a ZIP archive.
	
	@exclude - an array of items to exclude.
	*/
	function backupApp($exclude = array()){		
		$backupName = "doWantBackup_".date("Ymd").".zip";
		
		$zip = new ZipArchive();
		$res = $zip->open($this->options['filepath'].$backupName,ZipArchive::CREATE);
		
		//MySQL backup needs to be added in here as well.
				
		$exclude_defaults = array(".", "..", ".htaccess", ".DS_Store",".git",".gitignore","custom","uploads","generateUpdate.php","update.zip");
		$exclude_list = array_merge($exclude_defaults,$exclude);
		
		$this->recursiveCreateArchive("./", $zip, $exclude_list);
		
		//$zip->addFromString('manifest.json', json_encode($manifestArray));
		$zip->close();
	}
	
	
	/*
	Method: recursiveCreateArchive
	Recursively iterates through a directory tree and adds all items to a zip Archive.
		
	@dir - The directory to start in.
	@zipArchive - An instantiated ZipArchive Object.
	@zipdir - Optional: 
	
	*/
	function recursiveCreateArchive($dir, $zipArchive, $excludeArray, $zipdir = ''){
		
		if (is_dir($dir)) { 
			if ($dh = opendir($dir)) { 

				//Add the directory
				if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir); 
			   
				// Loop through all the files 
				while (($file = readdir($dh)) !== false) { 

					if(!in_array($file,$excludeArray)){            
					//If it's a folder, run the function again!
						if(!is_file($dir . $file)){
							// Skip parent and root directories 
							$this->recursiveCreateArchive($dir . $file . "/", $zipArchive, $excludeArray, $zipdir . $file . "/");
						}else{
							// Add the files
							$zipArchive->addFile($dir . $file, $zipdir . $file);
						}
					}
				}
			}
		}
	}
	
	
}
?>