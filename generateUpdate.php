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

$zip = new ZipArchive();

$filename = (isset($_REQUEST['name']))? $_REQUEST['name'].".zip" : "update.zip";

$_REQUEST['version'] = $_REQUEST['name'];

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





