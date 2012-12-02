<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Wishlist</title>

		<script src="jquery.min.js"></script>
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="bootstrap/css/bootstrap.icons-large.css" rel="stylesheet">

		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script>
			
			
			function setupAdminUser(){
				
				var username = $("input#username").val();
				if(username.length == 0){
					alert("Username must be filled in");
				}

				var fullname = $("input#fullname").val();
				if(fullname.length == 0){
					alert("Full Name must be filled in");
				}
				
				var password = $("input#password").val()
				var confirmPass = $("input#confirmPassword").val()
				
				if(password != confirmPass){
					alert("Password and Confirm Password must match.");
					return false;
				}
				
				var email = $("input#emailAddress").val();
				
				if(email.length == 0){
					alert("You must enter an email address");
					return false;
				}else if(email.match(/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/) == null){
					alert("You must enter a valid email address");
					return false;					
				}
								
				var args = {
					'userAction':'add',
					'username': username,
					'password': password,
					'fullname': fullname,
					'email': email,
					'approved': 1,
					'admin': 1
				};
				
				data = {
					"interact":'user',
					"action":'manageUser',
					"args":args
				}

				//Get the Categories.
				jQuery.post('ajaxCalls.php',data,function(response){	
					window.location ="index.php";
				},"json");
				
			}
			
			function createConfig(){
				var args = {
					'nodb':true,
					'host': $("#host").val(),
					'dbname': $("#dbname").val(),
					'dbuser': $("#dbuser").val(),
					'dbpass': $("#dbpass").val(),
					'table_prefix': $("#table_prefix").val(),
					'password_hasher': $("#password_hasher").val(),
					'filepath': $("#filepath").val(),
					'currency_symbol': $("#currency_symbol").val(),
				}
				
				var data = {
					'interact':'setup',
					'action':'generateConfigFile',
					'args':args
				}

				jQuery.post('ajaxCalls.php',data,function(response){
					console.log(response);
				},"json");						
			}
			
			function checkDirectoryWritable(){
				var data = {
					'interact':'setup',
					'action':'checkDirectoryWriteable',
					'args':{'dir':'.'}
				}
				$("#step-one div.alert").hide();
				
				jQuery.post('ajaxCalls.php',data,function(response){
					
					if(response == false){
						$("#step-one div.alert-error").fadeIn();						
					}else{
						$("#step-one div.alert-success").fadeIn();						
					}
				},"json");
			}
			
			
			
			jQuery(document).ready(function(){			
				//Setup the carousel and make sure it doesn't auto advance.
				$('#setupCarousel').carousel('pause').on('slid',function(){
					$('#setupCarousel').carousel('pause');
				});
				
				$("button#createConfig").click(function(){
					createConfig();
				});

				$("button#proceedButton").click(function(){
					checkDirectoryWritable();
				});
			
				
			
			});
		</script>
		<style>
			div#userFormBlock{
				margin-top:100px;				
			}
			
		</style>
	</head>	
	<body>
		<!--
		<div class="modal hide fade" id="errorResponse">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h2>...Well Something Went Wrong</h2>
			</div>
			<div class="modal-body">	
				<p id="errorMessage"></p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Close</a>
			</div>
		</div>
		-->
		
		<div class="row">
			<div class="span8 offset2">			
				<h2>Do Want! Setup</h2>
			</div>
		</div>
		<div id="userFormBlock" class="row">
			<div class="span8 offset2">
				<div id="setupCarousel" class="carousel slide">
					<div class="carousel-inner">
						<div id="step-one" class="active item">
							<p>Welcome to the Do Want! setup. Before getting too far you'll need to do a few things:</p>
							<div class="well">
								<ul>
									<li>Create a Database and User for Do Want to use</li>
									<li>Make the Do Want directory writeable by the server</li>
								</ul>
								<button class="btn btn-success" id="proceedButton">Check that the directory is writeable</button>

								<div id="directoryResponseSuccess" class="hide alert alert-success">
									Directory is writeable. Good Job!
								</div>
								<div id="directoryResponseFailure" class="hide alert alert-error">
									It doesn't look like the Do Want directory is writable by the web server. We'll need this if we're going to generate a configuration file. You will only need to leave it writable until the config file has been written.
								</div>
								
							</div>
						</div>
						<div class="item">
							<form id="configSetup" class="form-horizontal" onsubmit="return false;">
								<div class="well">
									<div class="control-group">
										<label class="control-label" for="host">Host</label>
										<div class="controls">
											<input type="text" id="host" class="input-medium" value="localhost"/>
										</div>
									</div>															
									<div class="control-group">
										<label class="control-label" for="dbname">Database Name:</label>
										<div class="controls">
											<input type="text" class="input-medium" id="dbname"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="dbuser">Database Username:</label>
										<div class="controls">
											<input class="input-medium" class="input-medium" type="text" id="dbuser"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="dbpass">Database Password:</label>
										<div class="controls">
											<input type="password" class="input-medium" id="dbpass"/>
										</div>
									</div>																								
									<div class="control-group">
										<label class="control-label" for="password_hasher">Password Hash:</label>
										<div class="controls">
											<select id="password_hasher" class="input-medium">
												<option value="MD5Hasher">MD5</option>
												<option value="NoHash">No Hash (not recommended)</option>
											</select>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="filepath">Upload Filepath:</label>
										<div class="controls">
											<input type="text" class="input-medium" id="filepath" value="uploads/"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="currency_symbol">Currency Symbol:</label>
										<div class="controls">
											<input type="text" class="input-mini" id="currency_symbol" value="$"/>
										</div>
									</div>						
									<button class="btn btn-success" id="createConfig">Create Configuration</button>
								</div>
							</form>
						</div>
				  		<div class="item">
							<p>Please fill in the form below to create the administrator user, you will be able to change these details and add more users later. After you submit, you'll be taken to the login page where you can log in and get started.</p>
							<div class="well">
								<form id="createUserform" class="form-horizontal" onsubmit="return false;">
									<div class="control-group">
										<label class="control-label" for="username">Username:</label>
										<div class="controls">
											<input type="text" class="input-medium" id="username"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="fullname">Full Name:</label>
										<div class="controls">
											<input type="text" class="input-medium" id="fullname"/>
										</div>
									</div>									
									<div class="control-group">
										<label class="control-label" for="password">Password:</label>
										<div class="controls">
											<input type="password" class="input-medium" id="password"/>
										</div>
									</div>									
									<div class="control-group">
										<label class="control-label" for="confirmPassword">Confirm Password:</label>
										<div class="controls">
											<input type="password" class="input-medium" id="confirmPassword"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="emailAddress">Email Address:</label>
										<div class="controls">
											<input type="text" class="input-medium" id="emailAddress"/>
										</div>
									</div>
									<div class="control-group">																																				
										<button class="btn btn-primary pull-right" onclick="setupAdminUser();">Create user</button>
									</div>
								</form>
							</div>
				  		</div>
					</div>

					<button class="btn btn-primary" onclick="$('#setupCarousel').carousel('next')">Next</button>
				</div>
			</span>
		</div>
	</body>
</html>
