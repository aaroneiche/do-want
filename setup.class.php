<?php
if (session_id() == "") session_start();

class setup extends db{

	/*
		Method: checkDirectoryWriteable
		Checks if a given directory can be written to. Returns true or false
		
		dir - the relative path of the directory in question.
	*/
	function checkDirectoryWriteable($args){
		error_log(is_writeable($args['dir']));
		return is_writable($args['dir']);
	}
	
	
	/*
		Method: generateConfigFile
		Takes arguments and creates a config file based on the default config file.
		
		host - the host for the db
		dbname - the database name
		dbuser - the database user
		dbpass - the database password
		table_prefix - a table prefix, if necessary
		password_hasher - preferred password hashing method
		filepath - relative path for file uploads
		currency_symbol - The preferred currency symbol.
		
	*/
	
	function generateConfigFile($args){
		$configFileName = "config.php"; //"config-test.php";
		$response = array();
		if(file_exists($configFileName)){
			$response['result'] = false;
			$response['message'] = "It appears you already have a config file.";
			return $response;
		}
		
		$defaultConfig = file_get_contents("config.default.php");

		$configItems = array(
			"dbhost = 'localhost'",
			"dbname = 'wishlist'",
			"dbuser = 'wishlist'",
			"dbpass = 'wishlist'",
			"options['table_prefix'] = ''",
			"options['password_hasher'] = 'MD5Hasher'",
			"options['filepath'] = 'uploads/'",
			"options['currency_symbol'] = '$'"
		);
		
		$populateItems = array(
			"dbhost = '{$args['host']}'",
			"dbname = '{$args['dbname']}'",
			"dbuser = '{$args['dbuser']}'",
			"dbpass = '{$args['dbpass']}'",
			"options['table_prefix'] = '{$args['table_prefix']}'",
			"options['password_hasher'] = '{$args['password_hasher']}'",
			"options['filepath'] = '{$args['filepath']}'",
			"options['currency_symbol'] = '{$args['currency_symbol']}'"
		);
		
		$fileReplace = str_replace ($configItems ,$populateItems , $defaultConfig);
		$fileWrite = file_put_contents($configFileName,$fileReplace);
		
		if($fileWrite !== false){
			$response['result'] = true;
			return $response;	
		}else{
			$response['result'] = false;
			return $response;
		}
	}

	/*
		Method: setupTables
		Gets SQL queries out of files in the SQL directory and generates tables.
	*/
	
	function setupTables($args){
		
		$queries = array();
		
		if ($handle = opendir('sql/')) {
		    /* This is the correct way to loop over the directory. */
		    while (false !== ($entry = readdir($handle))) {
				$fileNameEnd = strpos($entry,".sql");

				if($fileNameEnd != false){
					$queries[substr($entry,0,$fileNameEnd)] = file_get_contents("sql/".$entry);
				}
		    }
		    closedir($handle);
		}
		
		$queryResults = array();
		foreach($queries as $tableName => $queryToRun){
			$queryResults[$tableName] = $this->dbQuery($queryToRun);
		}
		
		$results = array(
			"result"=>true,
			"message"=>"The following tables were created",
			"tables" => $queryResults
		);
		
		return $results;
	}

	/*
		testDBCredentials
	*/
	function testDBCredentials($args){
		
		$this->dbhost = $args['host'];
		$this->dbname = $args['dbname'];
		$this->dbuser = $args['dbuser'];
		$this->dbpass = $args['dbpass'];
		
		$this->options = array("charSet"=>"utf8");
		
		$response = $this->dbConnect();
		
		return $response;
		
	}


	/*
	Generates PHP string of array code of the database structure
	This will be run on the current data base and the arrays will be compared to each other during and update.
	*/
	function generateTableStructure(){
		
		//$conn = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass); //localhost","root","root"
		//mysql_select_db($this->dbname);
		$this->dbConnect();
		$tableSet = array();
		
		$res = mysql_query("show tables");
		if($res){
			while($row = mysql_fetch_assoc($res)){
				$tableName = $row["Tables_in_".$dbname];
				$tableSet[$tableName] = array();
				
				$fieldNames = mysql_query("describe ".$tableName);
				error_log(print_r(mysql_fetch_assoc($fieldNames),true));
				while($fr = mysql_fetch_assoc($fieldNames)){			
					$tableSet[$tableName][$fr['Field']] = $fr;
				}
			}
		}else{
			error_log($res);
		}
		return $tableSet;
	}
}

?>