<?php 
session_start();
//	print_r($_SESSION);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Wishlist</title>

	<script src="jquery.js"></script>
	<script src="script.js"></script>
	<script>
		/* Lest you sneaky users think you can change this and gain access to your list, 
		 all db calls check session IDs, so messing with this value won't get you very far.*/
		
		userId = "<?php if(isset($_SESSION['userId'])) print $_SESSION['userId'] ?>";
	</script>
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	
</head>
<body>
<!--  -->
<div id="message" style="color:red"></div>

<?php 
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true)
	{
/*
	If we have a logged in user, let's take them to the site.
*/		
?>
	<script>
		jQuery(document).ready(function(){
			$(".tab").click(function(e){showSection(e)});
			getCurrentUserList();
			buildShopForSet();
			
			$("#myListTab").trigger("click");
		});
	</script>

	<button onclick="logout();">Logout</button>

<div id="curtain">&nbsp;</div>


<div id="mainContainer">
	<ul id="tabSet">
		<!-- Contains Navigation Tabs-->
		<li class="tab" data-openSection="myList" id="myListTab">
				My Wishlist
		</li>
		<li class="tab" data-openSection="otherLists">
				Other People's Lists
		</li>
		<li class="tab" data-openSection="shoppingList">
				My Shopping List
		</li>
		<li class="tab lastTab" data-openSection="manage">
				Manage
		</li>
	</ul>
	<div id="pageContainer">
		<div id="myList" class="section">
			<div id="userWishlistBlock">
				<button id="addItems"><img src="images/add.png" style="clear:both;"/><span id="addAnItemButtonText">Add An Item</span></button>
			<h3>User Wishlist</h3>
				<table id="userWishlist">
				</table>
			</div>
		
		</div>
		<div id="otherLists" class="section">
			<h3>List of users to shop for</h3>
		
			<div id="otherUserWishlistBlock">
				<h3>Other user Wishlist</h3>
				<select id="listOfUsers">
					<option selected> -- </option>
				</select>
				<table id="otherUserWishlist">
				</table>
			</div>			
		</div>
		<div id="shoppingList" class="section">
			
		</div>
		<div id="manage" class="section">
			
		</div>
	</div>
</div>


	
<?php
	}else{
	
/*
	Otherwise, we provide the login form.
*/
?>
	<form name="loginForm" id="loginForm" method="POST" onsubmit="return false;">
		
		Username: <input name="username" id="username" class="loginInput" /><br/>
		Password: <input name="password" id="password" type="password" class="loginInput" />
		<input type="submit" onclick="login();" value="login" />
		
	</form>
<?php 
}
?>	

</body>
</html>