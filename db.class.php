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
	
	function dbLastInsertId($resource = null){
		return mysql_insert_id($resource);
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
	
}

?>