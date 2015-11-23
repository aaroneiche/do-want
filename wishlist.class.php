<?php
if (session_id() == "") session_start();

class wishlist extends db{

	/*
	Method: getCurrentUserWishlist	
		Fetches and returns an array of items for the currently logged-in user.
		
	*/

	function getCurrentUserWishlist($args){
			// "select * from {$this->options['table_prefix']}items where userid = '{$_SESSION['userid']}'";
		if($args['includeReceived'] != 1){
			$received = "and items.received = 0";
		}else{
			$received = "";
		}
			
		$query ="select
					items.*, 
					(select Min(sourceprice) from itemsources where itemsources.itemid = items.itemid) as minprice,
					categories.category as displayCategory 
				from items 
				left join categories on categoryid = items.category
				where userid = '{$_SESSION['userid']}' $received";
		
		$result = $this->dbQuery($query);
		$list = $this->dbAssoc($result);
	
		return $list;
	}

	/*
	Method: getShoppingForList	
		Fetches and returns an array of items for the requested user id if current logged-in user is allowed to view.
		
		@shopForId - The user id for the requested user's shopping list.
	*/
	function getShoppingForList($args){
	
		if($args['shopForId'] == $_SESSION['userid']){
			return array(
				'responseType'=>"error",
				'title'=>'Shopping for yourself?',
				'message'=>"It looks like you just tried to shop for yourself. You can't do that!"
				);
				
		}else{
	
			$list = array();
		$query = "select 
			   items.itemid,
			   items.description,
			   items.ranking,
			   allocs.quantity as reservedByThisUser,
			   allocs.bought as boughtByThisUser,
				categories.category as displayCategory,
				(select min(sourceprice)
			          from itemsources
			          where itemsources.itemid = items.itemid)
			     as minprice,
				(select group_concat(source order by source SEPARATOR ', ' ) as sources from itemsources where itemsources.itemid = items.itemid) as sources,			
			   items.quantity,
				items.quantity -
					(select sum(allocs.quantity) from allocs where allocs.itemid = items.itemid)
			     as available
			from {$this->options['table_prefix']}items
				left join {$this->options['table_prefix']}categories on `categoryid` = items.`category`
				join {$this->options['table_prefix']}shoppers on shopper = '{$_SESSION['userid']}' and mayshopfor = '{$args['shopForId']}'
				left join {$this->options['table_prefix']}allocs on allocs.itemid = items.itemid and allocs.userid = '{$_SESSION['userid']}'
			where items.`userid` = '{$args['shopForId']}'
				and items.`received` = 0";
				
//		error_log($query);

			$result = $this->dbQuery($query);
			$list = $this->dbAssoc($result,true);

			if($list != null){

				foreach($list as $index => $listItem){
					
						if(!isset($listItem['reservedByThisUser'])){
							$list[$index]['reservedByThisUser'] = 0;
						}

						if(!isset($listItem['boughtByThisUser'])){		
							$list[$index]['boughtByThisUser'] = 0;
						}
				
						if(!isset($listItem['available'])){
							$list[$index]['available'] = $listItem['quantity'];							
						}
				
					}
				return $list;
			}
		}
	}
	
	
	/*
	Method: getShoppingList
		Gets a list of items which the current user has reserved.
		
		@userid - The ID of the user to get the list for.
	*/
	function getShoppingList($args){
		
		$cleanUserId = $this->dbEscape($args['userid']);

		/*		
		$query = "select {$this->options['table_prefix']}items.* from {$this->options['table_prefix']}items, {$this->options['table_prefix']}allocs where {$this->options['table_prefix']}items.itemid = {$this->options['table_prefix']}allocs.itemid and {$this->options['table_prefix']}allocs.`bought` != 1 and {$this->options['table_prefix']}allocs.`quantity` > 0 and allocs.userid = $cleanUserId";
		*/

		$query ="select 
			i.description, 
			u.fullname, 
			a.quantity, 
			(select min(sis.sourceprice)
				from itemsources as sis, items as si
				where sis.itemid = i.itemid) as bestPrice
		from 
			{$this->options['table_prefix']}items as i, 
			{$this->options['table_prefix']}allocs as a, 
			{$this->options['table_prefix']}users as u 
		where 
			i.itemid = a.itemid and 
			a.quantity > a.`bought` and 
			a.`quantity` > 0 and 
			a.userid = $cleanUserId and 
			u.userid = i.userid";

		$result = $this->dbQuery($query);
		if($this->dbRowCount($result) > 0){
			$shoppingList = $this->dbAssoc($result,true);
			
			return $shoppingList;
		}
	}
	
	
	/*
	Method: getCurrentCount	
		Gets the current quantity available for reserving and purchasing based on current user.
		
		int @itemid - The id of the item which is being affected
		int @userid - The id of the user affecting 
		string @allocateAction - purchase, reserve, return, release
		int @adjustment - Quantity to adjust by, positive or negative	
	*/
	
	function getCurrentCount($args){
	
		switch($args['allocateAction']){
			case 'purchase':
				/* 
					This query returns the reserved and purchased values for a particular item for a particular user.
					provides you with the number remaining purchasable.
					
					In order to buy, we're only checking the allocs table - not the items table.
					You must reserve before you buy.
				*/
			
				$query = "select 
						(
							{$this->options['table_prefix']}allocs.quantity - 
							{$this->options['table_prefix']}allocs.bought
						) as availableToBuy	
					from 
						{$this->options['table_prefix']}allocs
					where
						{$this->options['table_prefix']}allocs.itemid = '{$args['itemid']}' and
						{$this->options['table_prefix']}allocs.userid = '{$args['userid']}' 
						"; 
			break;
			case 'reserve':
				/*
					This query gets a total sum of all reservations for an item and the items requested quantity 
					and returns the difference.
					
					Provides you with the number remaining reservable.
				*/
			
				$query = "select 
						(
							{$this->options['table_prefix']}items.quantity - 
							sum({$this->options['table_prefix']}allocs.quantity)
						) as availableToReserve,
						{$this->options['table_prefix']}items.itemid as item,
						(select 
							count({$this->options['table_prefix']}allocs.quantity) 
						from 
							{$this->options['table_prefix']}allocs 
						where 
							{$this->options['table_prefix']}allocs.userid = '{$args['userid']}' and 
							{$this->options['table_prefix']}allocs.itemid = '{$args['itemid']}') 
						as userHasReserved
					from 
						{$this->options['table_prefix']}items, 
						{$this->options['table_prefix']}allocs
					where 
						{$this->options['table_prefix']}allocs.itemid = '{$args['itemid']}' and 
						{$this->options['table_prefix']}items.itemid = '{$args['itemid']}' 
						";			
			break;
			case 'return':
			case 'release':
				$query = "select 
							{$this->options['table_prefix']}allocs.* 
						from 
							{$this->options['table_prefix']}allocs
						where
							{$this->options['table_prefix']}allocs.itemid = '{$args['itemid']}' and 
							{$this->options['table_prefix']}allocs.userid = '{$args['userid']}'
						";
			break;
		}
			
			$result = $this->dbQuery($query);
			return $this->dbAssoc($result);	
	}
		
	/*
	Method: adjustReservedItem	
		Reserves, Releases, Purchases, or Returns item according to arguments provided 
		
		int @itemid - The id of the item which is being affected
		int @userid - The id of the user affecting 
		string @allocateAction - purchase, return, reserve, release
		int @adjustment - Unsigned Quantity to adjust by.
			
	*/	
	function adjustReservedItem($args){
		
		$query = "";
		$currentCount = $this->getCurrentCount($args);			
		
		switch($args['allocateAction']){
			case 'reserve':
				
				//Check if we've reserved any of this before.
				if($currentCount['userHasReserved'] > 0){
					//we check that we're not trying to reserve more than we can.
					if($currentCount['availableToReserve'] >= $args['adjustment']){						
						//we set our query to update to the new value
						$query = "update 
									{$this->options['table_prefix']}allocs 
								set 
									{$this->options['table_prefix']}allocs.quantity = 
										{$this->options['table_prefix']}allocs.quantity + {$args['adjustment']}
								where 
									{$this->options['table_prefix']}allocs.itemid = {$args['itemid']} and
									{$this->options['table_prefix']}allocs.userid = {$args['userid']}
									";
						
					}else{

						//we return an error saying the requested reservation is too high.
						$error = array("errorMessage"=>"Reservation quantity was greater than requested quantity.");
						return $error;
					}
					
				}else{
					//we insert the row into the alloc table.
					$query = "insert into {$this->options['table_prefix']}allocs(
								itemid,userid,bought,quantity
								) 
								values(
									'{$args['itemid']}',
									'{$args['userid']}',
									0,
									'{$args['adjustment']}'
								)";
				}
				
			break;
			case 'release':

				$availableToRelease = $currentCount['quantity'] - $currentCount['bought'];

				if($availableToRelease == $args['adjustment'] && $currentCount['bought'] == 0){
					//Delete the row - nothing is bought, and we're releasing all reservations
					$query = "delete from 
								{$this->options['table_prefix']}allocs					
							where 
								{$this->options['table_prefix']}allocs.itemid = {$args['itemid']} and
								{$this->options['table_prefix']}allocs.userid = {$args['userid']}
							";					
					
				}else if($availableToRelease >= $args['adjustment']){
					$query = "update 
								{$this->options['table_prefix']}allocs 
							set 
								{$this->options['table_prefix']}allocs.quantity = 
									{$this->options['table_prefix']}allocs.quantity - {$args['adjustment']}
							where 
								{$this->options['table_prefix']}allocs.itemid = {$args['itemid']} and
								{$this->options['table_prefix']}allocs.userid = {$args['userid']}
								";					
				}else{					
					$error = array("errorMessage"=>"Unpurchased Reserved quantity is less than requested adjustment");
					return $error;
				}
				
			break;
			case 'purchase':

				//Check to make sure that the reservation row exists.
				if(count($currentCount) > 0){
				
					//Check to make sure what we're not buying more than we've reserved.
					if($currentCount['availableToBuy'] >= $args['adjustment']){
						$query = "update 
									{$this->options['table_prefix']}allocs 
								set 
									{$this->options['table_prefix']}allocs.bought = 
										{$this->options['table_prefix']}allocs.bought + {$args['adjustment']}
								where 
									{$this->options['table_prefix']}allocs.itemid = {$args['itemid']} and
									{$this->options['table_prefix']}allocs.userid = {$args['userid']}
									";
					}else{
						//we return an error saying the requested purchase count is too high.
						$error = array("errorMessage"=>"Purchase quantity was greater than reserved quantity.");
						return $error;
					}
					
				}else{				
					$error = array("errorMessage"=>"You have not reserved this item.");
					return $error;					
				}
				
				
			break;
			case 'return':
				
				if($currentCount['bought'] >= $args['adjustment']){
					$query = "update 
									{$this->options['table_prefix']}allocs 
								set 
									{$this->options['table_prefix']}allocs.bought = 
										{$this->options['table_prefix']}allocs.bought - {$args['adjustment']}
								where 
									{$this->options['table_prefix']}allocs.itemid = {$args['itemid']} and
									{$this->options['table_prefix']}allocs.userid = {$args['userid']}
							";
				}else{			
					$error = array("errorMessage"=>"Trying to return more than you've bought!");
					return $error;					
				}
				
			break;
		}

		return $this->dbQuery($query);
	}
	
	/*
		Method: manageItemAll
		Updates all components of an item in a single call
		
	*/
	function manageItemAll($args){
		$itemManage = array();
		
		//error_log(print_r($args,true));

		$itemManage = $args;
		if(isset($args['id'])){
			$itemManage['itemid'] = $args['id'];
			$itemManage['itemAction'] = "edit"; 
			
		}else{
			$itemManage['itemAction'] = "add";	
		}
		
		//Return our item ID
		$manageResult = $this->manageItem($itemManage);
		$itemId = ($itemManage['itemAction'] == 'add')? $manageResult : $args['id'];
		
		
		
		$sourceResult = array();
		
		//Manage Sources
		if(count($args['sources']) > 0 ){
			foreach($args['sources'] as $source){
				if(isset($source['action'])){

					$sourceArgs = array();
					$sourceArgs['sourceid'] = $source['id'];					
					$sourceArgs['itemid'] = $itemId;
					$sourceArgs['source'] = $source['name'];
					$sourceArgs['sourceurl'] = $source['url'];
					$sourceArgs['sourceprice'] = $source['price'];
					$sourceArgs['sourcecomments'] = $source['comments'];
					
					$sourceArgs['itemSourceAction'] = $source['action'];
					$sourceResult[] = $this->manageItemSource($sourceArgs);
				}
			}
		}

		// Manage images.
		$imagesResult = array();
		
		if(count($args['images']) > 0){
			
			foreach($args['images'] as $image){
				if(isset($image['action'])){
					$image['itemid'] = $itemId;
					$image['itemImageAction'] = $image['action'];
					$imagesResult[] = $this->manageItemImage($image);
				}
			}
		}
	}
	
	
	/*
		Method: manageItem
		Adds, Edits or Deletes an item from the database.

		str @itemAction - The action name for managing an item: add,edit,or delete.
		
		int @userid - The id of the user who owns this item - note: does not have to be the logged in user
		str @description - The 'name' of the item 
		int @ranking - The rank according to user.
		int @category - integer based on categories table.
		str @comment - any comment the user wishes to share about the item.
		int @quantity - How many of this item the user would like.		
		
	*/
	
	function manageItem($args){
				
		switch($args['itemAction']){
			case 'add':
				$query = "insert into items(userid, description,ranking,category,comment,quantity)
						values(
							'{$_SESSION['userid']}',
							'{$this->dbEscape($args['description'])}',
							'{$this->dbEscape($args['ranking'])}',
							{$this->dbEscape($args['category'])},
							'{$this->dbEscape($args['comment'])}',
							'{$this->dbEscape($args['quantity'])}'
						)
				";			
			break;
			case 'edit':
				$query = "update items set 
						description = '{$this->dbEscape($args['description'])}',
						ranking = '{$this->dbEscape($args['ranking'])}',
						category = {$this->dbEscape($args['category'])},
						comment = '{$this->dbEscape($args['comment'])}',
						quantity = '{$this->dbEscape($args['quantity'])}'
					where
						itemid = {$this->dbEscape($args['itemid'])}
				";
			break;
			case 'delete':
				$query = "delete from items where itemid = {$this->dbEscape($args['itemid'])}";
				
				$reserveQuery = "select a.userid, u.fullname, i.`description` from {$this->options['table_prefix']}allocs as a, {$this->options['table_prefix']}users as u, {$this->options['table_prefix']}items as i where a.itemid = {$this->dbEscape($args['itemid'])} and i.itemid = a.itemid and u.userid = i.userid";
				
			break;			
		}
		
		if($args['itemAction'] == 'add'){
			$result = $this->dbQuery($query);
			$resultItemId = $this->dbLastInsertId();
						
			return $resultItemId;
			
		}else if($args['itemAction'] == 'delete'){

			$reserveResult = $this->dbQuery($reserveQuery);
				if($this->dbRowCount($reserveResult) > 0){
					$reservations = $this->dbAssoc($reserveResult,true);
															
					foreach($reservations as $user){
						$messageArgs = array(
							"senderId" => 0,
							"receiverId" => $user['userid'],
							"message" => "This email was sent to notify you that ".$user['fullname']." has deleted ".$user['description']." from his/her list. ",
							"forceEmail" => false);	
						$this->sendMessage($messageArgs);
					}
				}
				
			//Last, we delete the item itself.	
			$result = $this->dbQuery($query);
			return $result;		
		}else{
			$result = $this->dbQuery($query);			
			return $result;
		}
	}
	
	/*
		Method: manageItemSource
		This function adds or removes sources for a particular item.
		
		int @itemid - The itemid that this source is for
		string @itemSourceAction - What action to take: add, edit, delete
		int @sourceid - The id of the source for editing or deleting
		string @source - The name of the source: A store or website
		string @sourceurl - The URL for the source
		string @sourceprice - The price for the source
		string @sourcecomments - Comments for the source.
		int @addedByUserId - The userId who provided the source. - other users can offer sources for products.
	*/
	function manageItemSource($args){
		switch($args['itemSourceAction']){
			case 'add':
				$query = "insert into itemsources(itemid,source,sourceurl,sourceprice,sourcecomments,addedByUserId)
					values(
						'{$this->dbEscape($args['itemid'])}',
						'{$this->dbEscape($args['source'])}',
						'{$this->dbEscape($args['sourceurl'])}',																				
						'{$this->dbEscape($args['sourceprice'])}',
						'{$this->dbEscape($args['sourcecomments'])}',						
						'{$_SESSION['userid']}'
					)";
			break;
			case 'edit':
				$query = "update itemsources set
						source = '{$this->dbEscape($args['source'])}',
						sourceurl = '{$this->dbEscape($args['sourceurl'])}',
						sourceprice = '{$this->dbEscape($args['sourceprice'])}',
						sourcecomments = '{$this->dbEscape($args['sourcecomments'])}'
					where
						sourceid = '{$this->dbEscape($args['sourceid'])}' and
						addedByUserId = '{$_SESSION['userid']}'
				";
			break;
			case 'delete':
				$query = "delete from itemsources where sourceid = {$this->dbEscape($args['sourceid'])}";
			break;
			case 'setprimary':
			//Ideally we're going to move away from having this branch and just make the update statement more dynamic.
				$clearQuery = "update {$this->options['table_prefix']}itemsources i set i.primarysource = 0 where i.itemid = {$this->dbEscape($args['itemid'])}";
				
				$setQuery = "update {$this->options['table_prefix']}itemsources i set i.primarysource = 1 where i.sourceid = {$this->dbEscape($args['sourceid'])}";
				
				$this->dbQuery($clearQuery);
				$result = $this->dbQuery($setQuery);
				return $result;
			break;
		}

		$result = $this->dbQuery($query);
		error_log($result);
		return $result;
	}
	
	/*
		Method: manageItemImage
		int @itemid - The item this image will be associated with 
		string @filename - The name of the file that was uploaded.
		str @itemImageAction - The desired action: add or delete.
	*/	
	function manageItemImage($args){
			//print_r($args);
		$resultArray = array();	
			
		switch($args['itemImageAction']){
			case 'add':

				//Random name to prevent overwriting files.
//				$randName = substr(md5(uniqid(rand(), true)),0,10).$_FILES['uploadfile']['name'];				
//				$moveFile = move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->options['filepath'].$randName);

				$query = "insert into itemimages(itemid,filename) values(
					'{$this->dbEscape($args['itemid'])}',
					'{$this->dbEscape($args['filename'])}'
				)";
				
				$result = $this->dbQuery($query);
				$resultArray['itemId'] = $args['itemid'];
				
			break;
			case 'delete':
				$filenameQuery = "select filename from itemimages where imageid = '{$this->dbEscape($args['id'])}'";
				$filename = $this->dbValue($this->dbQuery($filenameQuery));
				
				if(file_exists($this->options['filepath'].$filename)){
					$deleteResult = unlink($this->options['filepath'].$filename);
				}else{
					$deleteResult = true;
				}
				
				$resultArray['filedeleted'] = $deleteResult;
				
							
				if($deleteResult){
					$query = "delete from itemimages where imageid = '{$this->dbEscape($args['id'])}'";
					$result = $this->dbQuery($query);
				}else{
					return $error = array('errorMessage'=>"There was a problem deleting the image file: ".$deleteResult);
				}		
			break;
		}

		$resultArray['queryResult'] = $result;		
		return $resultArray;
	}
	
	function uploadFile($args){
		
		$randName = substr(md5(uniqid(rand(), true)),0,10).$_FILES['uploadfile']['name'];
		$moveFile = move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->options['filepath'].$randName); 				
		
		$result = array();
		$result['fileUploaded'] = $moveFile;
		$result['fileName'] = $randName;
		
		return $result;
	}
	
	
	/*
		Method: markItemReceived
		Sets an item's "received" flag to 1.
	*/
	function markItemReceived($args){
		$query = "update items set 
			received = 1
		where
			itemid = {$this->dbEscape($args['itemid'])}";

		$result = $this->dbQuery($query);
		return $result;
	}
	
	/*
		Method: copyItem
		Copies an item to a particular user.
		
		int @userid - The ID of the user to copy the item to.
		int @itemid - The ID of the item to be copied.
		
	*/
	function copyItem($args){
		$itemQuery = "select 
						{$this->options["table_prefix"]}items.description, 
						{$this->options["table_prefix"]}items.ranking, 
						{$this->options["table_prefix"]}items.category 
					from 
						{$this->options["table_prefix"]}items 
					where 
						{$this->options["table_prefix"]}items.itemid = {$args['itemid']}";
		
		$itemImagesQuery = "select {$this->options["table_prefix"]}itemimages.* 
								from
									{$this->options["table_prefix"]}itemimages 
								where 
									{$this->options["table_prefix"]}itemimages.itemid = {$args['itemid']}";
									
		$itemSourcesQuery = "select {$this->options["table_prefix"]}itemsources.* 
								from {$this->options["table_prefix"]}itemsources 
								where {$this->options["table_prefix"]}itemsources.itemid = {$args['itemid']}";
				
		$item = $this->dbAssoc($this->dbQuery($itemQuery));
		$itemImages = $this->dbAssoc($this->dbQuery($itemImagesQuery));
		$itemSources = $this->dbAssoc($this->dbQuery($itemSourcesQuery));
		
		$itemInsertQuery = "insert into {$this->options["table_prefix"]}items(userid,description,ranking,category) 
								values('{$args['userid']}','{$item['description']}','{$item['ranking']}','{$item['category']}')";
		
		$copyResult = $this->dbQuery($itemInsertQuery);
		$copyItemId = $this->dbLastInsertId();

		
		if(count($itemImages) > 0){
			$imagesInsertQuery = "insert into {$this->options["table_prefix"]}itemimages(itemid, filename) values";
			foreach($itemImages as $num => $image){
				$imagesInsertQuery .= "('$copyItemId','{$image['filename']}')";
				if($num+1 < count($itemImages)){
					$imagesInsertQuery .=",";
				}
			}
			
			$copyImagesResult = $this->dbQuery($imagesInsertQuery);
		}
		
		if(count($itemSources) > 0){
			$sourcesInsertQuery = "insert into {$this->options["table_prefix"]}itemsources(itemid, source, sourceurl, sourceprice, sourcecomments) values";
			foreach($itemSources as $num => $source){
				$sourcesInsertQuery .= "('$copyItemId','{$source['source']}','{$source['sourceurl']}','{$source['sourceprice']}','{$source['sourcecomments']}')";
				if($num+1 < count($itemSources)){
					$sourcesInsertQuery .=",";
				}
			}

			$copySourcesResult = $this->dbQuery($sourcesInsertQuery);
		}		
		
		
	}
	
	
	/*
		Method: addItem
		A non-abstract version for adding items to the list in general - adds and item, then it's images and sources.
	*/
	function addItem($args){
		
		$itemId = manageItem($args);
		
				
	}
	
	/*
	Method: getItemDetails
	Fetches the item details for a particular item and returns them in a associative array.
	returns an associative array containing item info, images, sources, and alloc data.
	
	int @itemid - The item to request details about.
	*/
	
	function getItemDetails($args){
		$itemQuery = "select
			items.itemid,
			items.description as itemDescription,
			items.userid as itemOwner,
			items.ranking as itemRanking,
			items.category as itemCategory,
			items.comment as itemComment,
			items.quantity as itemQuantity,
			items.addedByUserId as itemAddedByUserId
			
			from items where itemid = '{$args['itemid']}'";

		$itemResult = $this->dbQuery($itemQuery);
		$itemDetailArray = $this->dbAssoc($itemResult,true);		
		
		$itemIsForUser = ($itemDetailArray[0]['itemOwner'] === $_SESSION['userid'])? true : false;
		
		if($this->options['logErrors'] == true){
			error_log("Wishlist.Class getItemDetails: Requested Item Owner: ".$itemDetailArray[0]['itemOwner']);
			error_log("Wishlist.Class getItemDetails: Session UserId: ".$_SESSION['userid']);
			error_log("Wishlist.Class getItemDetails: Item is for user flag: ".$itemIsForUser);
		}

		
					
		$sourcesQuery = "select
			itemsources.sourceid as itemSourceId,
			itemsources.source as itemSource,
			itemsources.sourceurl as itemSourceUrl,
			itemsources.sourceprice as itemSourcePrice,
			itemsources.sourcecomments as itemSourceComments

			from itemsources where itemsources.itemid = '{$args['itemid']}'";
			
			
		$sourcesResult = $this->dbQuery($sourcesQuery);

		$sourcesDetailArray = ($this->dbRowCount($sourcesResult) > 0 ) ? $this->dbAssoc($sourcesResult,true) : null;

		$imagesQuery = "select
			itemimages.imageid as itemImageId,
			itemimages.filename as itemImageFilename

			from itemimages where itemimages.itemid = '{$args['itemid']}'";

		$imagesResult = $this->dbQuery($imagesQuery);
		$imagesDetailArray = ($this->dbRowCount($imagesResult) > 0 ) ? $this->dbAssoc($imagesResult,true) : null;

		$allocQuery = "select
			allocs.itemid,
			allocs.userid as itemAllocUserId,
			users.fullname as itemAllocUserName,
			allocs.bought as itemAllocBought,
			allocs.quantity as itemAllocQuantity

			from allocs,users 
			where 
				allocs.itemid = '{$args['itemid']}' and
				users.userid = allocs.userid";

		$allocResult = $this->dbQuery($allocQuery);
		
		if($itemIsForUser !== true){
			$allocDetailArray = ($this->dbRowCount($allocResult) > 0) ? $this->dbAssoc($allocResult): null;
		}else{
			$allocDetailArray = "currentUser";
		}

		$item = $itemDetailArray[0];
		$item['sources'] = $sourcesDetailArray;
		$item['images'] = $imagesDetailArray;
		$item['allocs'] = $allocDetailArray;
		
		
		return $item;
	}
	
	/*
		Method: getCategories
		Fetches a list of categories with IDs and names
	*/
	function getCategories(){
		
		$query = "select c.* from categories c order by category";
		$categoryResult = $this->dbQuery($query);
		return $this->dbAssoc($categoryResult);
	}


	/*
		Method: getImages
		Fetches a list of images for a given item
		@itemId = The id of the item for the images you want.
	*/
	function getImages($args){
		$query = "select * from itemimages where itemid = '{$args['itemId']}'";
		$imageResult = $this->dbQuery($query);
		
		return $this->dbAssoc($imageResult);
	}

	/*
		Method: getSourceDetails
		Gets the source information for a source for an item.
		
		int @sourceId - The id of the source information for an item.
	*/
	function getSourceDetails($args){
		$query = "select * from itemsources where sourceid = {$this->dbEscape($args['sourceId'])}";
		$sourceResult = $this->dbQuery($query);
		
		return $this->dbAssoc($sourceResult);
	}

	/*
		Method: getRanks
		Fetches a list of Ranks, Rank titles.
	*/
	function getRanks(){
		$query = "select r.* from {$this->options['table_prefix']}ranks as r";
		return $this->dbAssoc($this->dbQuery($query));
	}

	/*
		Method: manageRank
		Updates a Rank
		TODO: Add in support for "renderedHTML" or similar solution
		
		@rankid - The ranking to use. This is also the sortColumn.
		@rankTitle - The display title of the rank.
	*/
	function manageRank($args){
		$cleanId = $this->dbEscape($args['rankid']); 
		$cleanRank = $this->dbEscape($args['rankTitle']);
		
		$query = "update {$this->options['table_prefix']}ranks set title = '$cleanRank' where ranking = '$cleanId'";
		$result = $this->dbQuery($query);
		return $result;		
	}

	/*
		Method: manageCategory
		Updates a Category
		
		@categoryid - The ID of the category to display
		@category - The display name of the category.
		
	*/
	function manageCategory($args){

		$cleanId = $this->dbEscape($args['categoryid']); 
		$cleanCat = $this->dbEscape($args['category']);
		
		if(isset($args['action']) && $args['action'] == 'add'){
			$query = "insert into {$this->options['table_prefix']}categories(category) values('$cleanCat')";
		}else if(isset($args['action']) && $args['action'] == 'delete'){
			//remove category from items
			$updateItemCatsQuery = "update items set category = null where category = $cleanId";
			$updateItems = $this->dbQuery($updateItemCatsQuery);
			
			$query = "delete from categories where categoryid = $cleanId";

		}else{	
			$query = "update {$this->options['table_prefix']}categories set category = '$cleanCat' where categoryid = '$cleanId'";
		}
		
		$result = $this->dbQuery($query);			
		return $result;
	}

}

?>