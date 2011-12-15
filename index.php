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
	<script src="galleria/galleria-1.2.5.min.js"></script>
	
	<script>
		/* Lest you sneaky users think you can change this and gain access to your list, 
		 all db calls check session IDs, so messing with this value won't get you very far.*/
		
		userId = "<?php if(isset($_SESSION['userId'])) print $_SESSION['userId'] ?>";
		storedData = {};
		
		//Setup our galleria theme, even though we won't do anything with it for a while.
		Galleria.loadTheme('galleria/themes/classic/galleria.classic.min.js');
		
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
			getCategories();
			
			$("#myListTab").trigger("click");
			
			jQuery("#addItems").click(function(event){
			
			});
			
		});
		
	</script>

	<button onclick="logout();">Logout</button>

<div id="curtain">&nbsp;</div>

<!-- Meant to hold all the stuff we want to. Some browsers don't like it when you stick random table rows outside of a table, so I made a bag of holding-->
<div id="bagOfHolding" style="display:none">
	<table id="bohTable">
		<tr id="itemDetailRow">
			<td colspan="6">
				<div id="itemDetailRowContent">
					<table border="1" width="100%">
						<tr>
							<td id="itemDetailInfoBox">
								<h3 id="itemDetailName" class="itemDetailContainer"></h3>
								<div id="itemDetailRanking" class="itemDetailContainer"></div>
								<div id="itemDetailAlloc" class="itemDetailContainer"></div>
							</td>
							<td id="itemDetailImageBox" rowspan="3" width="50%">
								<div id="imageDetailGallery" class="itemDetailContainer">
								</div>
							</td>
						</tr>
						<tr>
							<td id="itemDetailSourcesBox">
								<table id="itemDetailSourcesTable" width="100%" class="itemDetailContainer">
								</table>
							</td>
						</tr>
						<tr>
							<td id="itemDetailCommentsBox">
								<div id="itemDetailComment" class="itemDetailContainer">
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>

	

</div>

<div id="addEditItemForm">
	<div><label for="itemDescriptionInput">Item Description:</label><input id="itemDescriptionInput"></input></div>
	<div><label for="itemCategoryInput">Item Category:</label><select id="itemCategoryInput"></select></div>
	<div><label for="itemQuantityInput">Item Quantity:</label><input id="itemQuantityInput"></input></div>
	<div>
		<label for="itemDescriptionComment">Item Comment:</label>
		<textarea id="itemDescriptionComment"></textarea>
	</div>
	
</div>	


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
			<h3>My Wishlist</h3>
			<button id="addItems"><img src="images/add.png" style="clear:both;"/><span id="addAnItemButtonText">Add An Item</span></button>

			<div id="userWishlistBlock" class="tableBlock">
				<table id="userWishlist" class="listTable">
				</table>
			</div>
		</div>
		
		<div id="otherLists" class="section">
			<h3>List of users to shop for</h3>
			<select id="listOfUsers">
				<option selected> -- </option>
			</select>
			<h3>Other user Wishlist</h3>
			
			<div id="otherUserWishlistBlock" class="tableBlock">
				<table id="otherUserWishlist" class="listTable">
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