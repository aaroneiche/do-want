function login(){

	userVal = jQuery("#username").val();
	passVal = jQuery("#password").val();

	if(userVal == null || userVal.length == 0 || passVal == null || passVal.length == 0){
		displayMessage("Please Fill in all fields");
		return false;
	}

	data = {
		interact:'user',
		action:'loginUser',
		username: userVal, 
		password: passVal
	}
	
	jQuery.post('ajaxCalls.php',data,function(response){
		
		if(response == "true"){
			window.location.reload();
		}else{
			displayMessage("Incorrect Login");
		}
	});
}

function logout(){

	data = {
		interact:'user',
		action:'logoutUser'
	}
	
	jQuery.post('ajaxCalls.php',data,function(response){
		if(response){
			window.location.reload();
		}
	});
	
}

function getCurrentUserList(){
	data = {
		interact:'wishlist',
		action:'getCurrentUserWishlist'
	}
	
	jQuery.post('ajaxCalls.php',data,function(response){
		wishlistData = {};
		wishlistData.isCurrentUser = true;
		wishlistData.toolset = "edit";		
		wishlistData.list = response;		
		wishlistData.targetTable = "userWishlist";
		wishlistData.columns = [
			{"Description":"description"},
			{"Ranking":"ranking"},
			{"Price":"price"},
			{"Category":"category"},
			{"Tools":"tools"}
		];

		displayWishlist(wishlistData);
				
	},"json");
}


/*
Function: getUserWishlist
	fetches the wishlist of a particular user and displays it in the "otherUserWishlist" table.
	
	@forUserId - The id of the user you would like to get the list for.
*/
function getUserWishlist(forUserId){
	data = {
		interact:'wishlist',
		action:'getShoppingForList',
		args:{shopForId:forUserId}
	}
	
	jQuery.post('ajaxCalls.php',data,function(response){
		
		if(response.responseType != undefined && response.responseType == "error"){
			errorMessage(response);
			//return false;
		}else{
			wishlistData = {};
			wishlistData.isCurrentUser = false;
			wishlistData.toolset = "shop";
			wishlistData.list = response;		
			wishlistData.targetTable = "otherUserWishlist";
			wishlistData.columns = [
				{"Description":"description"},
				{"Ranking":"ranking"},
				{"Price":"price"},
				{"Category":"category"},
				{"Quantity":"quantity"},			
				{"Tools":"tools"}
			]; 
			displayWishlist(wishlistData);			
		}			
	},"json");	
}

/*

*/
function sortByRankingDesc(){	
}

function sortByRankingAsc(){
}

function sortByPriceDesc(){
}

function sortByPriceAsc(){
}

/*
Function: displayWishlist
	
	Builds and displays current User's wishlist. Requrires JSON userlist object.
	If the user listed in object is the current user, we can display edit buttons.
	
	@displayData
		boolean @isCurrentUser - the toggle to determine which toolset to display
		String @toolset - the name of the toolset to include (edit, shop)
		Object @list - the javascript object item list: contains information to build rows.
		String @targetTable - where to put this list. 
*/
debug = {};
function displayWishlist(displayData){
	debug = displayData;
	//The table we're plugging this into.
	table = $("#"+displayData.targetTable);	
	table.html("");
	
	/*
	Builds the Table header and puts the columns into a definable order.
	*/
	hRow = $(document.createElement("tr"));
	
	for(key in displayData.columns){
	    for(colName in displayData.columns[key]){
			hRow.append(
				$(document.createElement("th")).append(colName)
			);
		}
	}
	table.append(hRow);

	//Loop through each item on the user list and add it to a row, which is then added to the table.
	$(displayData.list).each(function(i,e){
		row = $(document.createElement("tr"));
		if(i % 2 != 0){
			row.addClass("zebraRow");
		}
		
		//Values that need to be processed before the cell is built should be done here:
		e.ranking = renderRanking(e.ranking);
		e.tools = renderItemTools(e,displayData.toolset);
	
		/*
		This loops through our table structure and puts the data in the right order. Allows users
		to change the column order, or add/remove columns if they care to without resorting to the
		code. There will need to be a tool to change column order to make this valuable.
		*/	
		
		for(key in displayData.columns){
		    for(colName in displayData.columns[key]){
				row.append(
					$(document.createElement("td")).append(e[displayData.columns[key][colName]])
				);
		    } 
		}

		table.append(row);
	});	
}

/*
Function: renderRanking
	Currently takes an integer and turns it into a series of asterisks. In the future this can render an 
	image.
	
	int @rankValue - The ranking of an item as a number.
*/
function renderRanking(rankValue){
	var rankReturn = "";
	while(rankValue > 0){
		rankReturn +="*";
		rankValue--;
	}
	return rankReturn;
}

/*
Function renderItemTools
	Produces HTML buttons/icons for interacting with the item in the row.
	
	JS Object @itemObject - A Javascript object returned from the wishlist system.
	JS Object @toolInfo - A Javscript object with owner,  
*/
function renderItemTools(itemObject, toolInfo){
	
	toolBox = $(document.createElement("div")).attr("data-itemId",itemObject.itemid);
	
	switch(toolInfo){
		
		case "edit":
		
			itemReceive = $(document.createElement("img")).attr("src","images/refresh_nav.gif");
			itemEdit = $(document.createElement("img")).attr("src","images/write_obj.gif");
			itemDelete = $(document.createElement("img")).attr("src","images/cross.png");		

			itemReceive.click(function(){
				alert("Marked Received: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			itemEdit.click(function(){
				alert("Edit: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			itemDelete.click(function(){
				alert("Delete: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			toolBox.append(itemReceive);
			toolBox.append(itemEdit);
			toolBox.append(itemDelete);				
		break;
		case "shop":
		
			//Reserve, Copy, Buy, Return
		
			itemReserve = $(document.createElement("img")).attr("src","images/lock_co.gif");
			itemCopy = $(document.createElement("img")).attr("src","images/toolbar_replace.gif");
			itemReturn = $(document.createElement("img")).attr("src","images/cross.png");		
			itemBuy = $(document.createElement("img")).attr("src","images/step_done.gif");		


			itemReserve.click(function(){
				alert("Reserve: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			itemCopy.click(function(){
				alert("Copy: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			itemReturn.click(function(){
				alert("Return: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			itemBuy.click(function(){
				alert("Buy: "+
				this.parentNode.getAttribute("data-itemId"));
			});

			toolBox.append(itemReserve);
			toolBox.append(itemCopy);
		break;		
	}
	
	return toolBox;
	
}

/*
Function displayError
	Displays an error returned from the system (Client or Serverside) to the user.
	
	object @errorObject
		@title - The errorMessage's title.
		@message - The related Error message.
*/

function displayError(errorObject){
	alert("Uh-Oh: "+errorObject.title+" Message:"+errorObject.message);
}






