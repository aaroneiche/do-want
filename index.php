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
		
		/* This is really hacky, but it's the easiest way to call this and not break the existing methodology.*/
		
		storedData.categories = jQuery.parseJSON('<?php
			$_REQUEST["action"] = "getCategories";
			$_REQUEST["interact"] = "wishlist";
			include("ajaxCalls.php");
			unset($_REQUEST);
		?>');
		
		storedData.columns = {
			"Description":{
				"displayColumn":"displayDescription",
				"altDisplay":"--",
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
				"displayColumn":"minprice",
				"altDisplay":"--",				
				"sortFunctions":[
					sortByPriceAsc,
					sortByPriceDesc
				]
			},
			"Category":{
				"displayColumn":"displayCategory",
				"altDisplay":"--",				
				"sortFunctions":[]
			},
			"Tools":{
				"displayColumn":"displayToolbox",
				"sortFunctions":[]
			},
		};

		$(document).ready(function(){
			$("#tabSetContainer a")
				.click(function(e){showSection(e);})
				.button();
			
			$("#addItems").click(function(){
				clearManageItemForm();
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
			//getCategories({func:buildCategorySelect,args:[storedData.categories,"#itemCategoryInput"]});
			
			buildRankSelect(5,"#itemRankInput");
			buildCategorySelect(storedData.categories,"#itemCategoryInput");

			jQuery("#myCarousel").carousel('pause');

			jQuery("#backToUsersLink").click(function(){
				jQuery("#myCarousel").carousel('prev');
			});

			jQuery("#itemSubmit").click(function(){
				manageItem();
			});

			
			
			jQuery("#myListTab").trigger("click");
						
		});
		
	</script>
<?php /* print_r($_SESSION); */?>

	<button class="btn" onclick="logout();">Logout</button>

<div class="modal hide fade" id="manageItemFormBlock">
	<form id="manageItemForm" class="form-horizontal" onsubmit="return false;">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h2>Manage Item</h2>
	  </div>
	  <div class="modal-body">
		<input type="hidden" id="itemId" />
		<div class="control-group"><label class="control-label" for="itemDescriptionInput">Item Description:</label><div class="controls"><input id="itemDescriptionInput"/></div></div>
		<div class="control-group"><label class="control-label" for="itemRankingInput">Item Rank:</label><div class="controls"><select id="itemRankInput"></select></div></div>				
		<div class="control-group"><label class="control-label" for="itemCategoryInput">Item Category:</label><div class="controls"><select id="itemCategoryInput"></select></div></div>
		<div class="control-group"><label class="control-label" for="itemQuantityInput">Item Quantity:</label><div class="controls"><input id="itemQuantityInput" class="input-mini"/></div></div>
		<div class="control-group">
			<label class="control-label" for="itemCommentInput">Item Comment:</label>
			<div class="controls">
				<textarea id="itemCommentInput"></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="itemSourcesEdit">Sources:</label>
			<div class="controls">
				<select id="itemSourcesEdit" multiple="multiple"></select>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" id="itemSubmit" class="btn btn-primary">Save changes</a>
	</div>
  </form>
</div>


<div class="modal hide fade" id="itemDetailsModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Item Details</h3>
	<table border="1" width="100%" class="table table-bordered">
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
    <a href="#" class="btn" data-dismiss="modal">Close</a>
  </div>
</div>

<div class="modal hide fade" id="itemSourceFormBlock">
	<form id="itemSourceForm" class="form-horizontal" onsubmit="return false;">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h2>Manage Source</h2>
	  </div>
	  <div class="modal-body">
		<input type="hidden" id="itemId" />
		<input type="hidden" id="sourceId" />
		<div><label for="sourceName">Source Name:</label><input id="sourceName"/></div>
		<div><label for="sourceUrl">Source URL:</label><input id="sourceUrl"/></div>
		<div><label for="sourcePrice">Source Price:</label><input id="sourcePrice"/></div>
		<div>
			<label for="sourceComments">Source Comments:</label>
			<textarea id="sourceComments"></textarea>
		</div>
	  </div>
	  <div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" id="itemSubmit" class="btn btn-primary">Save changes</a>
	  </div>
  </form>
</div>

<div class="row">
	<div id="tabSetContainer" class="span8 offset2">		
		<div class="btn-group" data-toggle="buttons-radio">
			<a href="#" class="btn" id="myListTab" data-section="myList">My Wishlist</a>
			<a href="#" class="btn" id="otherListsTab" data-section="otherLists">Other People's Lists</a>
			<a href="#" class="btn" id="shoppingListTab" data-section="shoppingList">My Shopping List</a>
				<?php if($_SESSION['admin'] == 1){ ?>
			<a href="#" class="btn" id="manageTab" data-section="manage">Manage</a>
				<?php } ?>
		</div>		
	</div>
</div>
<div class="row">
	<div id="pageBlock" class="span8 offset2">
		<div id="myList" class="section">
			<h2>My Wishlist</h2>
			<button id="addItems" class="btn">Add Item</button>

			<div id="userWishlistBlock" class="tableBlock">
				<table id="userWishlist" class="table table-striped table-bordered table-condensed">
				</table>
			</div>
		</div>
		
		<div id="otherLists" class="section">
			<!--
			<h2>List of users to shop for</h2>
			<select id="listOfUsers" class="">
				<option selected> -- </option>
			</select>
			<h2>Other user Wishlist</h2>
			-->
			<div id="myCarousel" class="carousel slide">
			  <!-- Carousel items -->
			  <div class="carousel-inner">
				<div class="active item">
					<h2>People I'm shopping for.</h2>
					<!-- <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a> -->
					Click on a User to see their wishlist.
					<table id="listOfUsersTable" class="table table-striped table-bordered table-condensed">
					</table>					
				</div>
				<div class="item">
					<h2>Wishlist:</h2>
					<a class="btn" href="#" id="backToUsersLink" data-slide="prev"><i class="icon-arrow-left"></i> Back to Users</a>
					<div id="otherUserWishlistBlock" class="tableBlock">
						<table id="otherUserWishlist" class="table table-striped table-bordered table-condensed">
						</table>
					</div>	
				</div>
			  </div>
			  <!-- Carousel nav -->
			  
			  
			</div>

			
		</div>
		<div id="shoppingList" class="section">
			Shopping List
		</div>
	<?php if($_SESSION['admin'] == 1){ ?>
		<div id="manage" class="section">
			Admin (not visible for non-admin users)
		</div>
	<?php } ?>
	</div>
</div>
	
<?php
	}else{
	
/*
	Otherwise, we provide the login form.
*/
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