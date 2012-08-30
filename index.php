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
		
		userId = "<?php if(isset($_SESSION['userid'])) print $_SESSION['userid'] ?>";
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
				$('#openAddImageForm').addClass('disabled').prop("disabled","disabled");
				$('#manageItemFormBlock').modal();
			});
			
			
			$("#requestAccount").click(function(){
				$("#manageUser input#userId").val("");
				$("#manageUser input#userAction").val("add");
				$("#manageUserSaveButton").html("Request Account");

				$("#userFormBlock").modal('show');
			});
						
			
			//Moved into general script section to provide form action when not logged in.
			$("#manageUserSaveButton").click(function(){				
				updateUserData();
			});			
			
			
			//binds firing the update images event to the loading of the relevant iframe.
			//Most of this should be rewritten into a method on script.js
			$("#uploadframe").load(function(){
				uploadResult = $.parseJSON($("#uploadframe").contents().text());
				if(uploadResult.queryResult == true){
					$("#uploadAlert").addClass("alert-success");
					$("#uploadAlertMessage").append("The file upload is complete");
					populateImagesOnForm(uploadResult.itemId);
				}else{
					$("#uploadAlert").addClass("alert-error");
					$("#uploadAlertMessage").append("The file upload encountered some problems. Please try again.")				
				}
				$("#uploadAlert").show();
			});
			
		});
		
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

			getCurrentUserList();
			buildShopForSet();
						
			buildRankSelect(5,"#itemRankInput");
			buildCategorySelect(storedData.categories,"#itemCategoryInput");

			jQuery("#myCarousel").carousel('pause');

			jQuery("#backToUsersLink").click(function(){
				jQuery("#myCarousel").carousel('prev');
			});

			jQuery("#itemSubmit").click(function(){
				manageItem();
			});
			
			$("#openManageUserForm").click(function(){
				$("#manageUserSaveButton").html("Update Account");				
				populateManageUserForm(userId);
			});
			
			$("#openAddImageForm").click(function(){
				
				var forItemId = $("#openAddImageForm").attr("data-forItemId")
				populateImagesOnForm(forItemId);
				
				$("#manageItemFormBlock").modal('hide');
				$('#itemImagesFormBlock').modal('show');

				//Sets the itemIdForImage to the ID we're editing.
				$("#itemIdForImage").val(forItemId);
			});


<?php
	//admin-only javascript load calls;
	if($_SESSION['admin'] == 1){
?>
			displaySystemUsers();
			
			$("#deleteObjectForm #deleteSubmit").click(function(){
				deleteUserFromSystem($("#deleteObjectForm #deleteObjectId").val());
			});
			
<?php
}
?>

			//This is the point that loads which tab the user is on. This will eventually be a choose-able option.
			jQuery("#myListTab").trigger("click");
						
		});
		
	</script>
<?php /* print_r($_SESSION); */?>

	<button class="btn" onclick="logout();">Logout</button>

<div class="modal hide fade" id="manageItemFormBlock">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h2>Manage Item</h2>
	  </div>
	  <div class="modal-body">
		<form id="manageItemForm" class="form-horizontal" onsubmit="return false;">
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
			<div class="control-group">
				<div class="controls">
					<button id="openAddImageForm" class="btn btn-primary">Manage Images</button>
				</div>
			</div>			
			
			
  		</form>
	</div>

	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" id="itemSubmit" class="btn btn-primary">Save changes</a>
	</div>

</div>

<div class="modal hide fade" id="itemDetailsModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Item Details</h3>
	</div>
	<div class="modal-body">

	<table border="1" width="100%" class="table table-bordered">
		<tr>
			<td id="itemDetailInfoBox">
				<h3 id="itemDetailName" class="itemDetailContainer"></h3>
				<div id="itemDetailRanking" class="itemDetailContainer"></div>
				<div id="itemDetailAlloc" class="itemDetailContainer"></div>
			</td>
			<td id="itemDetailImageBox" rowspan="3" width="50%">
				<div id="itemDetailImageCarousel" class="carousel slide">
					<div class="carousel-inner">
					</div>
					 <a class="left" href="#itemDetailImageCarousel" data-slide="prev"><i class="icon-arrow-left"></i></a>
					 <a class="right" href="#itemDetailImageCarousel" data-slide="next"><i class="icon-arrow-right"></i></a>
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

<div class="modal hide fade" id="itemImagesFormBlock">

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h2>Manage Images</h2>
	  </div>
	  <div class="modal-body">
			<!--	<form id="itemImagesForm" class="form-horizontal" onsubmit="return false;"> -->
			<form method="POST" id="imageUploadForm" enctype="multipart/form-data" action="ajaxCalls.php" target="uploadframe" >
				
			<input type="hidden" name="interact" value="wishlist">
			<input type="hidden" name="action" value="manageItemImage">
			<input type="hidden" name="itemImageAction" value="add">
			<input type="hidden" name="itemid" id="itemIdForImage" value="1"/><br>
			
			<div class="control-group"><label class="control-label" for="uploadfile">Select A file for upload</label><div class="controls"><input type="file" name="uploadfile" id="uploadfile" class="input"/></div></div>
			<button type="submit" class="btn btn-primary">Upload Image</button>
			</form>

			<div id="uploadAlert" class="alert">
			  <a class="close" data-dismiss="alert" href="#">×</a>
			  <span id="uploadAlertMessage"></span>
			</div>
			<div id="currentImagesBlock">
				
			</div>	
			<!-- iframe for uploading files - this is hidden via CSS. -->
			<iframe id="uploadframe" name="uploadframe"></iframe>
	  </div>
	  <div class="modal-footer">
		<a href="#" id="manageImageCloseButton" class="btn" data-dismiss="modal">Close</a>
	  </div>
</div>

<div class="modal hide fade" id="deleteConfirmBlock">
	<form id="deleteObjectForm">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h2>Are you Sure?</h2>
		</div>
		<div class="modal-body">
			<input type="hidden" id="deleteObjectId" />
			<input type="hidden" id="deleteType" />
			<p id="deleteWarningMessage">
				Warning! You are deleting a user on the system. If you continue, they will not be able to log in or access any of their items.
				A Deleted user cannot be recovered!
			</p>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			<a href="#" id="deleteSubmit" class="btn btn-danger">Yes, Delete</a>
		</div>
	</form>
</div>



<div class="row">
	<div id="tabSetContainer" class="span8 offset2">		
		<div class="btn-group" data-toggle="buttons-radio">
			<a href="#" class="btn" id="myListTab" data-section="myList">My Wishlist</a>
			<a href="#" class="btn" id="otherListsTab" data-section="otherLists">Other People's Lists</a>
			<a href="#" class="btn" id="shoppingListTab" data-section="shoppingList">My Shopping List</a>
			<a href="#" class="btn" id="manageTab" data-section="manage">Manage</a>
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
			<div id="myCarousel" class="carousel slide">
			  <!-- Carousel items -->
			  <div class="carousel-inner">
				<div class="active item">
					<h2>People I'm shopping for.</h2>
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
		<div id="manage" class="section">
			User Settings
			<button id="openManageUserForm" class="btn btn-primary">Change my settings</button>
			
						
			<?php if($_SESSION['admin'] == 1){ ?>
			This Section visible only to Admins.
			
			<table id="adminUserList" class="table table-striped table-bordered table-condensed">
				
			</table>
			List of Categories
			
			List of Ratings
			<?php } ?>
		</div>
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
				<button type="submit" onclick="login();" value="login" class="btn btn-primary">Login</button>
			</form>			
		</div>
		
	</div>
	<div class="row" id="additionalInfo">
		<!-- A great place for extra buttons, messages, etc. -->
		<div class="span4 offset4">
			<button id="requestAccount" type="button" class="btn btn-small">Request an Account</button> <button type="button" class="btn btn-small disabled" disabled>I Forgot my password</button>
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
<!-- As we need the User Form available for requesting an account, we have to have the form outside the limit-->	
<div class="modal hide fade" id="userFormBlock">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h2>Manage User</h2>
	  </div>
	  <div class="modal-body">
		<form id="manageUser" class="form-horizontal" onsubmit="return false;">
			<input type="hidden" id="userId" />
			<input type="hidden" id="userAction" />
			
			<div class="control-group">
				<label class="control-label" for="">Username</label>
				<div class="controls">
					<input class="input-medium" type="text" id="username"/>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="userPassword">Password</label>
				<div class="controls">
					<input class="input-medium passwordSet" type="password" id="userPassword"/>
					<span class="help-inline"></span>					
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="userPasswordConfirm">Confirm Password</label>
				<div class="controls">
					<input class="input-medium passwordSet" type="password"  id="userPasswordConfirm"/>
					<span class="help-inline"></span>					
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="userFullName">Full Name</label>
				<div class="controls">
					<input class="input-medium" type="text"  id="userFullName"/>
					<span class="help-inline"></span>					
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="emailAddress">Email Address</label>
				<div class="controls">
					<input class="input-medium" type="text"  id="emailAddress"/>
					<span class="help-inline"></span>					
				</div>
			</div>
			<?php if($_SESSION['admin'] == 1){ ?>
				<div class="control-group">
					<label class="control-label" for="userApproved">User is Approved</label>
					<div class="controls">
						<input type="checkbox" id="userApproved"/>
						<span class="help-inline"></span>						
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="userIsAdmin">User may Administer</label>										
					<div class="controls">
						<input type="checkbox" id="userIsAdmin"/>
						<span class="help-inline"></span>
					</div>
				</div>
			<?php }?>																
 		</form>

	  </div>
	  <div class="modal-footer">
		<a href="#" id="manageUserCloseButton" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" id="manageUserSaveButton" class="btn btn-primary">Save</a>
	  </div>
</div>
</div>	
</body>
</html>