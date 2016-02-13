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
		require 'vendor/autoload.php';
		require "config.php";

		$app = new Phinx\Console\PhinxApplication();
		$wrap = new Phinx\Wrapper\TextWrapper($app, array("configuration"=>"phinx_conf.php","parser"=>"php"));

		$execution = call_user_func([$wrap, "getMigrate"], 'production', "20151101000000");
		if($execution){
			$results = array(
				"result"=>true,
				"message"=>"Table migration run",
				"tables" => ""
			);
		}else{
			$results = array(
				"result"=>false,
				"message"=>$execution,
				"tables" => ""
			);
		}			

		$seed = call_user_func([$wrap, "getSeed"]);
		if($seed){
			$results["message"] .= ", Tables Seeded";
		}else{
			$results["message"] .= ", Tables Seeding Failed";
		}
		        
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
	
}

?>