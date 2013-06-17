<?php

	$createFile = false;

	$dbname = "dowant";
	$testConn = mysql_connect("localhost","root","root");
	mysql_select_db($dbname);
	$tableSet = array();
	
	$res = mysql_query("show tables");
	
	while($row = mysql_fetch_assoc($res)){
		$tableName = $row["Tables_in_".$dbname];
		$tableSet[$tableName] = array();
		
		$fieldNames = mysql_query("describe ".$tableName);
		while($fr = mysql_fetch_assoc($fieldNames)){			
			$tableSet[$tableName][$fr['Field']] = $fr;
		}
	}

	if(!$createFile){
		include("sql_struct");		
		//$updateStructure = array();
	}else{
		file_put_contents("sql_struct","<?php \$updateStructure = ".var_export($tableSet,true)." ?>");
	}
	
	function checksumArray($arr) {
		return md5(serialize($arr));
	}
	
	if(!$createFile){
		$arrayTest = (checksumArray($updateStructure) == checksumArray($tableSet));
		error_log(($arrayTest)? "arrays match" : "arrays don't match");
	
		if(!$arrayTest){
//			print checksumArray($updateStructure);
//			print $updateStructure;
//			print "<br/><br/><br/>";
//			print checksumArray($tableSet);
//			print $tableSet;
		}else{

		}
	}
?>