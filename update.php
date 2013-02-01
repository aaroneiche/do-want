<body style="font-family:arial">
<?php
//This is an update test file.

function copyUpdateFiles($fileArray,$zipArchive){

	foreach($fileArray as $file){
	
		if(!isset($file['name'])){
			copyUpdateFiles($file,$zipArchive);
		}else{
			$fileChecksum = md5_file($file['path'].$file['name']);
			if($fileChecksum == $file['checksum']){
				print $file['name']." is up-to-date.<br/>";
			}else{
				print $file['name']." <b>Should be updated.</b> ";
				
				$result = $zipArchive->extractTo($file['path'], $file['name']);
				
				if($result){
					print $file['name']." copied and replaced.<br/>";
				}else{
					print "Copy failed<br/>";
				}
			}
		}
	}	
}

$zip = new ZipArchive;
$res = $zip->open("uploads/update.zip");

if($res == true){
	
	$manifestLocation = $zip->locateName("manifest.json");
	
	$manifestFile = $zip->getFromIndex($manifestLocation);
	$manifest = json_decode($manifestFile,true);
	
	copyUpdateFiles($manifest,$zip);
	
}

?>
</body>