<?php
if (session_id() == "") session_start();

class setup extends db{

	/*
		Method: checkDirectoryWriteable
		Checks if a given directory can be written to. Returns true or false
		
		dir - the relative path of the directory in question.
	*/
	function checkDirectoryWriteable($args){
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
		Check 
	*/
}

?>