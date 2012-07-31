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

	<script src="jquery.min.js"></script>
	<script src="script.js"></script>
	<!-- <script src="galleria/galleria-1.2.5.min.js"></script> -->

    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	
	<link href="style.css" rel="stylesheet">
	
	<style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
		

	
	<script>
		/* Lest you sneaky users think you can change this and gain access to your list, 
		 all db calls check session IDs, so messing with this value won't get you very far.*/
		
		userId = "<?php if(isset($_SESSION['userId'])) print $_SESSION['userId'] ?>";
		storedData = {};
		
		storedData.columns = {
			"Description":{
				"displayColumn":"displayDescription",
				"sortFunctions":[
					sortByDescriptionDesc,
					sortByDescriptionAsc
				]
			},
			"Ranking":{
				"displayColumn":"displayRanking",
				"sortFunctions":[
					sortByRankingAsc,
					sortByRankingDesc
				]				
			},
			"Price":{
				"displayColumn":"price",
				"sortFunctions":[
					sortByPriceAsc,
					sortByPriceDesc
				]
			},
			"Category":{
				"displayColumn":"category",
				"sortFunctions":[]
			},
			"Tools":{
				"displayColumn":"displayToolbox",
				"sortFunctions":[]
			},
		};

		$(document).ready(function(){
			$("#tabSetContainer button")
				.click(function(e){showSection(e);})
				.button();
			
			$("#addItems").click(function(){
				$('#manageItemFormBlock').modal();
			});
			
			
		})
		
		
		//Setup our galleria theme, even though we won't do anything with it for a while.
		//Galleria.loadTheme('galleria/themes/classic/galleria.classic.min.js');
		
	</script>
	
	<!-- <link rel="stylesheet" href="style.css" type="text/css" /> -->
	
</head>
<body>
<!--  -->
<div class="container">

<?php 
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true)
	{
/*
	If we have a logged in user, let's take them to the site.
*/		
?>
	<script>
		jQuery(document).ready(function(){
			
			//jQuery(".tab").click(function(e){showSection(e)});

			getCurrentUserList();
			buildShopForSet();
			
			//Calls getCategories with a callback to populate the category select on the item form.
			getCategories({func:buildCategorySelect,args:[storedData.categories,"#itemCategoryInput"]});
			
			buildRankSelect(5,"#itemRankInput");
			
			
			
			jQuery("#myListTab").trigger("click");
			
			jQuery("#addItems").click(function(event){
			});
			
			jQuery("#itemSubmit").click(function(){
				manageItem();
			});
		});
		
	</script>

	<button class="btn" onclick="logout();">Logout</button>

<div id="curtain">&nbsp;</div>

<!-- Meant to hold all the stuff we want to. Some browsers don't like it when you stick random table rows outside of a table, so I made a bag of holding-->
<div id="bagOfHolding" style="display:none">
	<table id="bohTable">
		<tr id="itemDetailRow">
			<td colspan="6">
				<div id="itemDetailRowContent">
					
				</div>
			</td>
		</tr>
	</table>

</div>

<div class="modal hide fade" id="manageItemFormBlock">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Manage Item</h3>
  </div>
  <div class="modal-body">
	<form id="manageItemForm" class="form-horizontal" onsubmit="return false;">
		<input type="hidden" id="itemId" />
		<div><label for="itemDescriptionInput">Item Description:</label><input id="itemDescriptionInput"/></div>
		<div><label for="itemRankingInput">Item Rank:</label><select id="itemRankInput"></select></div>				
		<div><label for="itemCategoryInput">Item Category:</label><select id="itemCategoryInput"></select></div>
		<div><label for="itemQuantityInput">Item Quantity:</label><input id="itemQuantityInput"/></div>
		<div>
			<label for="itemCommentInput">Item Comment:</label>
			<textarea id="itemCommentInput"></textarea>
		</div>
		
		<input id="itemSubmit" type="submit" value="submit" >
	</form>

  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="#" class="btn btn-primary">Save changes</a>
  </div>
</div>


<div class="modal hide fade" id="itemDetailsModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Item Details</h3>
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
  <div class="modal-body">
	
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="#" class="btn btn-primary">Save changes</a>
  </div>
</div>



<div class="row">
	<div id="tabSetContainer" class="btn-group span6 offset3" data-toggle="buttons-radio">
		<button class="btn" id="myListTab" data-section="myList">My Wishlist</button>
		<button class="btn" id="otherListsTab" data-section="otherLists">Other People's Lists</button>
		<button class="btn" id="shoppingListTab" data-section="shoppingList">My Shopping List</button>
		<button class="btn" id="manageTab" data-section="manage">Manage</button>
	</div>
</div>
<div class="row">
	<div id="pageBlock" class="span8 offset2">
		<div id="myList" class="section">
			<h3>My Wishlist</h3>
			<button id="addItems" class="btn">Add Item</button>

			<div id="userWishlistBlock" class="tableBlock">
				<table id="userWishlist" class="table table-striped table-bordered table-condensed">
				</table>
			</div>
		</div>
		
		<div id="otherLists" class="section">
			<h3>List of users to shop for</h3>
			<select id="listOfUsers" class="">
				<option selected> -- </option>
			</select>
			<h3>Other user Wishlist</h3>
			
			<div id="otherUserWishlistBlock" class="tableBlock">
				<table id="otherUserWishlist" class="table table-striped table-bordered table-condensed">
				</table>
			</div>			
		</div>
		<div id="shoppingList" class="section">
			Shopping List
		</div>
		<div id="manage" class="section">
			Admin (not visible for non-admin users)
		</div>
	</div>
</div>

<!--
<div id="mainContainer">



	<div id="tabSetContainer">
		
		<ul id="tabSet">
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
	
	</div>
	<div id="pageContainer">
	</div>
</div>

-->
	
<?php
	}else{
	
/*
	Otherwise, we provide the login form.
*/
error_log("this is a test");
?>

	<div class="row">
		<div class="span4 offset4">
			<form name="loginForm" id="loginForm" method="POST" onsubmit="return false;" class="form-inline">
				<input name="username" id="username" type="text" class="input-small" placeholder="Username"/>
				<input name="password" id="password" type="password" class="input-small" placeholder="Password" />
				<button type="submit" onclick="login();" value="login" class="btn">Login</button>
			</form>			
		</div>
		
	</div>
	<div class="row" id="alertLocation">
		
		
		
		<!--
		<div id="displayAlert" class="span4 offset4 alert">
			<span id="loginAlertMessage"></span>
			<a class="close" data-dismiss="alert" href="#">&times;</a>
		</div>
		-->
	</div>
<?php 
}
?>	
</div>	
</body>
</html>