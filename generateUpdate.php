<pre>
<?php


if(isset($_REQUEST['gen'])){

function addFolderToZip($dir, $zipArchive, $zipdir = '', $manifest){ 
	$exclude_list = array(".", "..", ".htaccess", ".DS_Store",".git",".gitignore","uploads","custom", "generateUpdate.php","update.zip");
	
    if (is_dir($dir)) { 
        if ($dh = opendir($dir)) { 

            //Add the directory
            if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir); 
           
            // Loop through all the files 
            while (($file = readdir($dh)) !== false) { 

				if(!in_array($file,$exclude_list)){            
                //If it's a folder, run the function again! 
	                if(!is_file($dir . $file)){
	                    // Skip parent and root directories 
						$manifest[$file] = addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . $file . "/","", $manifest);
	                }else{
						$manifest[$file] = array(
							"path" => $dir,
							"name" => $file,
							"checksum" => md5_file($dir.$file)
						);
					
	                    // Add the files 
	                    $zipArchive->addFile($dir . $file, $zipdir . $file);
	                }
				}
            }
			return $manifest;
        }
    }
}


/*
Generates PHP string of array code of the database structure
This will be run on the current data base and the arrays will be compared to each other during and update.

*/
function generateTableStructure($dbname, $host, $username, $password){

	$conn = mysql_connect($host, $username, $password); //localhost","root","root"
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
	
	return $tableSet;
}


$zip = new ZipArchive();

$filename = (isset($_REQUEST['name']))? $_REQUEST['name'].".zip" : "update.zip";

if(!isset($_REQUEST['version'])){
	print "Version not defined";
	return false;
}
//$_REQUEST['version'] = $_REQUEST['name'];

$tableGen = file_put_contents("db.struct","<?php \$updateStructure = ".var_export(generateTableStructure("dowant", "localhost", "root", "root"),true)." ?>");

$res = $zip->open($filename, ZipArchive::CREATE);
$manifestFileArray = addFolderToZip("./",$zip,"",array());

$mainfestData = array(
	'version'=>$_REQUEST['version'],
	'files'=>$manifestFileArray
);

$zip->addFromString('manifest.json', json_encode($manifestData));
$zip->close();

}

?>
If you'd like to create an update, click <a href="generateUpdate.php?gen=gen">here</a>.

</pre>





