<?php
session_start();

$classFileNames = array(
	"wishlist.class.php",
	"user.class.php",
	"setup.class.php"
);

$optionSet = array();
$jsonArray = array();

foreach($classFileNames as $file){
	$currentFile = file_get_contents($file); //get the file contents.
	$methodNames = array(); //Instantiate an array to store matches in.
	preg_match_all("`/\*\s+?Method:(.+?)$(.+?)\*/`ms",$currentFile,$methodNames);
	
	$className = substr($file,0,strlen(strstr($file,".",true)));
		
	foreach($methodNames[1] as $key => $method){
		
		$jsonArray[trim($method)] = array(
			'name' => trim($method),
			'comments' => $methodNames[2][$key],
			'classToCall' => $className
		);
		
		
		if(isset($_REQUEST['submit']) && $_REQUEST['submit'] =='submit'){
			$selected = (trim($method) == $_REQUEST['action'])?"selected":"";
		}
		
		$optionSet .="<option value=\"".trim($method)."\" {$selected}>{$className} - {$method}</option>";
	}	
}

?>
<script>
	methodsNotes =
	<?php 
		print json_encode($jsonArray);
	?>;
	

function getParams(methodNotes){
		var pattern = /@(.+?)\s-\s/g;  
		var match;
		
		var valLocation = document.getElementById("nameValueSet");
		valLocation.innerHTML = "";
		
		while (match = pattern.exec(methodNotes))
		{
			label1 = document.createElement("label");
			label1.innerHTML = match[1];
			nameInput = document.createElement("input");
			nameInput.setAttribute("name","argName[]")
			nameInput.setAttribute("type","hidden");
			
			nameInput.value = match[1]
			
			valInput = document.createElement("input");
			//valInput.name = "argVal[]";
			valInput.setAttribute("name","argVal[]");

			valLocation.appendChild(nameInput);
			valLocation.appendChild(label1);
			valLocation.appendChild(valInput);
			valLocation.appendChild(document.createElement("br"));												
		}
	}
		
	function displayNotes(methodName){
		element = document.getElementById("methodStuff").innerHTML = methodsNotes[methodName].comments;
		document.getElementById("interact").value = methodsNotes[methodName].classToCall;
		getParams(methodsNotes[methodName].comments);
			
	}
	
	
</script>

<form name="sendForm" method="POST" >
<table>
<tr><td colspan="2">Current User: <?php print $_SESSION['fullname']?> Is Admin? <?php print ($_SESSION['admin']==1)? "Yes":"No" ?> </td></tr>
<tr><td valign="top">
	<input type="hidden" id="interact" name="interact"/>
	<label for="action">Action</label>
	<select id="action" name="action" onchange="displayNotes(this.value);">
		<?php 
			print $optionSet;
		?>
	</select><br><br>
<input type='submit' name="submit" value="submit"/>	
</td>
<td id="nameValueSet" valign="top" style="width:200px;">

<br><br>
</td>
<td>
<textarea id="methodStuff" cols=50 rows=12>
</textarea>

</td>
</tr>
<tr><td valign="top">
Data sent to page:
<?php
print "<pre>";
print_r($_REQUEST);
print "</pre>";
?>
</td><td colspan="2">
Database response:
<div  style="overflow-y:scroll; height: 350px; border:solid black 1px; padding:4px;">
<?php
if(isset($_REQUEST['submit']) && $_REQUEST['submit'] =='submit'){
	
	if(count($_REQUEST['argName']) > 0){
		$_REQUEST['args'] = array();
	
		foreach($_REQUEST['argName'] as $key => $name){
			$_REQUEST['args'][$name] = $_REQUEST['argVal'][$key];
		}
	}
	
	function __autoload($class_name) {
	    require_once strtolower($class_name . '.class.php');
	}

	//We need the configuration
	require_once("config.php");

	//Create new instance of the 
	$instance = new $_REQUEST['interact']();

	$instance->dbhost = $dbhost;
	$instance->dbname = $dbname;
	$instance->dbuser = $dbuser;
	$instance->dbpass = $dbpass;
	$instance->options = $options;

	$instance->dbConnect();

	print "<pre>";
	print_r($instance->$_REQUEST['action']($_REQUEST['args']));
	print "</pre>";
}

?>
</td></tr>
</table>
</form>