<?php
if(isset($_REQUEST['submit']) && $_REQUEST['submit'] =='submit'){
	
	print_r($_REQUEST);
	
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
	print_r($instance->$_REQUEST['action']($_REQUEST));
	print "</pre>";
}

?>
<form method="POST" enctype="multipart/form-data"  >

<!-- <input type="hidden" name="" value=""> -->

<input type="hidden" name="interact" value="wishlist">
<input type="hidden" name="action" value="manageItemImage">

<!--
userid<input name="userid" value="1"/><br>
description<input name="description" value="test item"/><br>
ranking<input name="ranking" value="4"/><br>
category<input name="category" value="1"/><br>
comment<input name="comment" value="my comment"/><br>
quantity<input name="quantity" value="1"/><br>
-->

<input type="hidden" name="itemImageAction" value="add">
ItemId: <input name="itemid" value="1"/><br>
File: <input type="file" name="uploadfile" id="uploadfile">
	
<input name="submit" type="submit" value="submit">
</form>