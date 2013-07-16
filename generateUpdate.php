<pre>
<?php

function __autoload($class_name) {
    require_once strtolower($class_name . '.class.php');
}

if(isset($_REQUEST['gen'])){
	/*
		Recursively moves through the directory structure of the project and adds it to the supplied archive.
	*/
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
	
	$zip = new ZipArchive();
	
	$filename = (isset($_REQUEST['name']))? $_REQUEST['name'].".zip" : "update.zip";
	
	switch($_REQUEST['a']){
		case "update":
			if(!isset($_REQUEST['version'])){
			//$_REQUEST['version'] = $_REQUEST['name'];
				print "Version not defined";
				return false;
			}	
		break;
		case "db":
		
			include("config.php");
			$set = new setup();
			$set->dbhost = $dbhost;
			$set->dbname = $dbname;
			$set->dbuser = $dbuser;
			$set->dbpass = $dbpass;
			
			$tableGen = file_put_contents("db.struct","<?php \$updateStructure = ".var_export($set->generateTableStructure(),true)." ?>");
			error_log(print_r($tableGen,true));
			if($tableGen){
				print "DB Structure created and placed in db.struct";
			}else{
				print "DB Structure was <b>not</b> created. Please review logs for errors.";
			}
			return false;
		break;
	}
	
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





