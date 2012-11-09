<?php 
session_start();
//	print_r($_SESSION);

define("VERSION","0.75");

?>
<!DOCTYPE html>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<!-- These are the responsive tags, here so I can flip the switch later. -->
<!--
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
-->

	<title>Wishlist</title>

	<script src="jquery.min.js"></script>
	<script src="script.js"></script>
	<!-- <script src="galleria/galleria-1.2.5.min.js"></script> -->

    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	<script src="bootstrap/js/bootstrap-typeahead.js"></script>	

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
		

		/*
			Eventually we'll store this information in the DB somewhere so it can be changed at a whim.
		*/

		storedData.columns = [
			{
				"columnName":"Description",
				"displayColumn":"displayDescription",
				"altDisplay":"--",
				"sortFunctions":[
					sortByDescriptionDesc,
					sortByDescriptionAsc
				]
			},
			{
				"columnName":"Ranking",
				"displayColumn":"displayRanking",
				"sortFunctions":[
					sortByRankingAsc,
					sortByRankingDesc
				]				
			},
			{
				"columnName":"Price",
				"displayColumn":"minprice",
				"altDisplay":"--",				
				"sortFunctions":[
					sortByPriceAsc,
					sortByPriceDesc
				],
				"displayStyles":"floatRight"
			},
			{
				"columnName":"Category",
				"displayColumn":"displayCategory",
				"altDisplay":"--",				
				"sortFunctions":[]
			},
			{
				"columnName":"Tools",
				"displayColumn":"displayToolbox",
				"sortFunctions":[]
			},
		];


		storedData.columns2 = storedData.columns.slice(0);
		storedData.columns2.splice(4,0,{
			"columnName":"Status",
			"displayColumn":"displayStatus",
			"sortFunctions":[]
		});
		
		storedData.modalTree = [];

		$(document).ready(function(){
			$("#tabSetContainer a")
				.click(function(e){showSection(e);})
				.button();
			
			$("#addItems").click(function(){
				clearManageItemForm();
				$('#openAddImageForm').addClass('disabled').prop("disabled","disabled");
				$('#manageItemFormBlock').modal();
				//swapModal("#manageItemFormBlock");
			});
			
			
			$("#requestAccount").click(function(){
				$("#manageUser input#userId").val("");
				$("#manageUser input#userAction").val("add");
				$("#manageUserSaveButton").html("Request Account");

				$("#userFormBlock").modal('show');
			});
			
			//System information accessible by the version number in the lower righthand corner.			
			$("#versionNumber").click(function(){
				$("#systemInformation").modal('show');
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

<div id="loading" class="popover">
	<div class="popover-inner">
		<div class="popover-title"><h5>Loading</h5></div>
		<div class="popover-content"><img src='images/ajax-loader.gif'></div>
	</div>
</div>

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
			
			jQuery("#itemSourceSubmit").click(function(){
				manageItemSource();
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

			displayShopForMeList();
			setupUserSearch();
			getMessagesForUser(userId,0);
			
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
				<table id="itemSourcesEdit" class="controls table-striped table-bordered table-condensed">
					
				</table>
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
					 <a class="left" href="#itemDetailImageCarousel" data-slide="prev"><i class="icon-chevron-left"></i></a>
					 <a class="right" href="#itemDetailImageCarousel" data-slide="next"><i class="icon-chevron-right"></i></a>
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
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h2>Manage Sources</h2>
	</div>
	<div class="modal-body"> 
		<form id="itemSourceForm" class="form-horizontal" onsubmit="return false;">
			<input type="hidden" id="itemId" />
			<input type="hidden" id="sourceId" />
		
			<div class="control-group">
				<label class="control-label" for="sourceName">Source Name:</label>
				<div class="controls">
					<input type="text" id="sourceName"/>
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label" for="sourceUrl">Source URL:</label>
				<div class="controls">
					<input type="text" id="sourceUrl"/>
				</div>				
			</div>
		
			<div class="control-group">
				<label class="control-label" for="sourcePrice">Source Price:</label>
				<div class="controls">
					<input type="text" id="sourcePrice"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="sourceComments">Comments:</label>
				<div class="controls">
					<textarea type="text" id="sourceComments"></textarea>
				</div>
			</div>
			
		</form>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Cancel</a>
		<a href="#" id="itemSourceSubmit" class="btn btn-primary">Save changes</a>
	</div>
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
			<a href="#" class="btn" id="manageTab" data-section="manage">
				Manage<span id="messageIndicator">&nbsp;<span id="messagesIcon" class="badge badge-important"><i class="icon-envelope icon-white"></i></span>&nbsp;</span>
			</a>
		</div>
	</div>
</div>
<div class="row">
	<div id="pageBlock" class="span8 offset2">
		<div id="myList" class="section">
			<h2>My Wishlist</h2>
			<button id="addItems" class="btn btn-primary">Add Item</button>

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
					<a class="btn  btn-primary" href="#" id="backToUsersLink" data-slide="prev"><i class="icon-arrow-left icon-white"></i> Back to Users</a>
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
			<h2>User Settings</h2>
			<div class="row">
				<div class="span4">
					<button id="openManageUserForm" class="btn btn-primary">Change my settings</button>					
				</div>
				<div class="span4">
					<h3>Messages</h3>
					<table id="userMessages" class="table table-striped table-bordered table-condensed">
						
					</table>
				</div>				
			</div>
			<div class="row">
				<div class="span4">			

					<h3>People Shopping for me.</h3>
					<table id="shoppingForMe" class="table table-striped table-bordered table-condensed">
						
					</table>
				</div>
				<div class="span4">			

					
					<h3>People I'm Shopping For:</h3>
					<table id="currentShopFor" class="table table-striped table-bordered table-condensed">
						
					</table>
					Search for a user to add: <input type="text" id="shopForSearch" class="typeahead">					
				</div>
			</div>			
			<?php if($_SESSION['admin'] == 1){ ?>
			<div class="row">
				<div class="span8">
					<h3>Users</h3>
					<table id="adminUserList" class="table table-striped table-bordered table-condensed">

					</table>
				</div>
			</div>			
			<div class="row">
				<div class="span4">
					<h3>Categories</h3>
					
				</div>
				<div class="span4">
					<h3>Ratings</h3>					
				</div>				
			</div>
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
		<div id="displayAlert" class="span4 offset4 alert">
			<span id="loginAlertMessage"></span>
			<a class="close" data-dismiss="alert" href="#">&times;</a>
		</div>
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
				<div class="control-group">
					<label class="control-label" for="emailMessages">Email messages</label>
					<div class="controls">
						<input type="checkbox" id="emailMessages"/>
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

	<div class="modal hide fade" id="systemInformation">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h2>About Do-Want</h2>
		  </div>
		  <div class="modal-body">
			<h2>Do-want</h2> 
			<h4>Version <?php print VERSION; ?></h4>
			<p>Do-want is a wishlist management system that helps families and friends organize gift exchanges.</p>
			<p>Do-Want is released under a GPL 2.0 License</p>
			<p>For more information, visit <a href="http://code.google.com/p/do-want/" target="_blank">http://code.google.com/p/do-want/</a></p>
		  </div>
		  <div class="modal-footer">
		  </div>
	</div>
				
	<div class="modal hide fade" id="message">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h2>Message</h2>
		  </div>
		  <div class="modal-body">
			
		  </div>
		  <div class="modal-footer">
		  </div>
	</div>
</div>

<a href="#" id="versionNumber">v<?php print VERSION; ?></a>
</body>
</html>