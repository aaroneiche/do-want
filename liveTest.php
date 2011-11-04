<?php
session_start();

$wishlist = file_get_contents("wishlist.class.php");
$user = file_get_contents("user.class.php");

$wlMethodNames = array();
preg_match_all("`/\*\s+?Method:(.+?)$(.+?)\*/`ms",$wishlist,$wlMethodNames);

$uMethodNames = array();
preg_match_all("`/\*\s+?Method:(.+?)$(.+?)\*/`ms",$user,$uMethodNames);

$options = "";

$methodList = array_merge($wlMethodNames[1],$uMethodNames[1]);
$commentsList = array_merge($wlMethodNames[2],$uMethodNames[2]);
	
$jsonArray = array();

foreach($methodList as $key=> $method){
	$jsonArray[trim($method)] = $commentsList[$key];
}


foreach($methodList as $method){
	$options .="<option value=\"".trim($method)."\">$method</option>";
}

?>
<script>
	methodsNotes =
	<?php 
		print json_encode($jsonArray);
	?>;
	
	function displayNotes(methodName){
		element = document.getElementById("methodStuff").innerHTML = methodsNotes[methodName];
	}
	
	
</script>

<table>
<tr><td valign="top">
<form name="sendForm" method="POST" >
	<label for="interact">Interact</label>
	<select name="interact">
		<option value="wishlist">Wishlist</option>
		<option value="user">User</option>		
	</select>
	<br><br>
	<label for="action">Action</label>
	<select name="action" onchange="displayNotes(this.value);">
		<?php 
			print $options;
		?>
<!--
		<option value="loginUser">User - LoginUser</option>
		<option value="logoutUser">user - LogoutUser</option>
		<option value="getShopForUsers">user - getShopForUsers</option>		
		<option value="getCurrentUserWishlist">wishlist - getCurrentUserWishlist</option>
		<option value="getShoppingForList">wishlist - getShoppingForList</option>
		<option value="getCurrentCount">wishlist - getCurrentCount</option>
		<option value="adjustReservedItem">wishlist - adjustReservedItem</option>
		<option value="manageItem">wishlist - manageItem</option>
		<option value="manageItemSource">wishlist - manageItemSource</option>
		<option value="manageItemImage">wishlist - manageItemImage</option>
		<option value="getItemDetails">wishlist - getItemDetails</option>
	-->	
	</select><br><br>
<input type='submit' name="submit" value="submit"/>	
</td>
<td>
	Name:<input name="argName[]"/> 
	Val:<input name="argVal[]"/>	
<br>
	Name:<input name="argName[]"/>
	Val:<input name="argVal[]"/>	
<br>
	Name:<input name="argName[]"/>
	Val:<input name="argVal[]"/>	
<br>
	Name:<input name="argName[]"/>
	Val:<input name="argVal[]"/>	
<br>
	Name:<input name="argName[]"/>
	Val:<input name="argVal[]"/>	
<br><br>
</form>
</td>
<td>
<textarea id="methodStuff" cols=50 rows=8>
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
</td><td>
Database response:
<div  style="overflow-y:scroll; height: 500px; border:solid black 1px; padding:4px;">
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