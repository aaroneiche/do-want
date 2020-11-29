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
		
			//Support for defaults on ajax calls using .ajax instead of .post
			jQuery.ajaxSetup({
				url: 'ajaxCalls.php',
				type: 'POST'
			});

			
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
				}else if(email.match(/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/) == null){
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
					if(response){
						$("#step-four div.alert-success").fadeIn();
						$("#stepFourNextButton").prop("disabled",false).removeClass("disabled");
						
						$("button#setupAdminUserButton").prop("disabled",true).addClass("disabled");

					}else{
						$("#step-four div.alert-error").fadeIn();
					}
				},"json");
				
			}
			
			function getConfigFields(){
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
				
				if(args['host'].length == 0){
					alert("You must fill in a host");
					return false;
				}
				if(args['dbname'].length == 0){
					alert("You must fill in a Database name");
					return false;
				}
				if(args['dbuser'].length == 0){
					alert("You must fill in a database username");
					return false;
				}
				if(args['dbpass'].length == 0){
					alert("You must fill in a database password");
					return false;
				}
				
				return args;
			}
						
			function createConfig(){
				var configFields = getConfigFields();
	
				if(configFields == false){
					return false;
				}
				
				var data = {
					'interact':'setup',
					'action':'generateConfigFile',
					'args': configFields
				}
				$("div.configResponseSuccess").fadeOut();
			
								
				jQuery.post('ajaxCalls.php',data,function(response){
					if(response.result == true){
						$("#step-two div.alert-success").html("Configuration File has been created").fadeIn();
						
						$("button#createConfig").prop("disabled",true).addClass("disabled");
						$("#stepTwoNextButton").prop("disabled",false).removeClass("disabled");
					}else{
						if(response.result != true){
							$("#step-two div.alert-error").html(response.message).fadeIn();
						}
					}
				
				},"json");						
			}
			
			function checkDirectoryWritable(){
				var data = {
					'interact':'setup',
					'action':'checkDirectoryWriteable',
					'args':{'dir':'.',
							'nodb':true
					}
				}
				
				jQuery.ajax({
					'data':data
				}).done(function(response,textStatus){
					if(response){
						$("#step-one div.alert-success").fadeIn();
						$("#stepOneNextButton").prop("disabled",false).removeClass("disabled");
						
						$("#directoryWriteableCheckButton").prop("disabled",true).addClass("disabled");
					}else if(!response){
						$("#step-one div.alert-error").fadeIn();
					}else{
						$("#step-one div.alert-error").html("Response from server is unknown.").fadeIn();
					}
				}).fail(function(response,textStatus){
					$("#step-one div.alert-error").html("There was an issue communicating with the server.").fadeIn();
				});
				
				$("#step-one div.alert").hide();
			}
				
			function setupTables(){
				var data = {
					'interact':'setup',
					'action':'setupTables'
				}

				jQuery.post('ajaxCalls.php',data,function(response){
					if(response.result == true){
						//tablesResponseSuccess	
											
						$("#step-three div.alert-success").html("Tables have been created!").fadeIn();
						
						$("#createTables").prop("disabled",true).addClass("disabled");
						$("#stepThreeNextButton").prop("disabled",false).removeClass("disabled");
					}else{
						if(response.message != null){
							$("#step-three div.alert-error").html(response.message);
						}
						$("#step-three div.alert-error").fadeIn();
					}
				},"json");				
			}

			function checkDatabaseConnect(){
			
				var configFields = getConfigFields();
				
				if(!configFields){
					return false;
				}
				
				$("#configResponseFailure").hide();
							
				jQuery.ajax({
					"data":{
						"interact":"setup",
						"action":"testDBCredentials",
						"args":configFields
					},
					"dataType":"json"
				}).done(function(response,responseCode){
					
					
					if(response != true){
						$("#configResponseFailure").html("There was an error trying to communicate with the database: "+response.message).fadeIn();
					}else{
						$("#checkCredentials").hide();
						$("#createConfig").fadeIn();
						$("#configResponseSuccess").html("I successfully connected to the DB. Another win! <b>Go ahead and click that \"Create Config\" button</b> and we'll keep moving.").fadeIn();	
					}
				})
			}
			
			jQuery(document).ready(function(){
				//Setup the carousel and make sure it doesn't auto advance.
				$('#setupCarousel').carousel('pause').on('slid',function(){
					$('#setupCarousel').carousel('pause');
				});
				
				$("button#createConfig").click(function(){
					createConfig();
				});

				$("button#checkCredentials").click(function(){
					checkDatabaseConnect();
				});
				
				
				$("button#directoryWriteableCheckButton").click(function(){
					checkDirectoryWritable();
				});

				$("button#createTables").click(function(){
					setupTables();
				});			
			
				$("#setupAdminUserButton").click(function(){
					setupAdminUser();
				});
			
				$(".nextStepButton").click(function(){
					$('#setupCarousel').carousel('next');
				});
			});
		</script>
		<style>
			div#userFormBlock{
				margin-top:100px;				
			}
			
			input[type="text"],input[type="password"]{
				height:30px;
			}
			
			#createConfig{
				display:none;
			}
			
		</style>
	</head>	
	<body>
	
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

								<div id="directoryResponseSuccess" class="hide alert alert-success">
									Directory is writeable. Good Job! Go ahead and press the next button and we'll setup a config file.
								</div>
								<div id="directoryResponseFailure" class="hide alert alert-error">
									It doesn't look like the Do Want directory is writable by the web server. We'll need this if we're going to generate a configuration file. You will only need to leave it writable until the config file has been written.
								</div>
								<div>
									<button id="directoryWriteableCheckButton" class="btn btn-success">Check that the directory is writeable</button>								
									<button id="stepOneNextButton" class="btn btn-primary pull-right nextStepButton disabled" disabled>Next</button>
								</div>
							</div>
						</div>
						<div id="step-two" class="item">
							<form id="configSetup" class="form-horizontal" onsubmit="return false;">
								<p>
									Enter your database and connection details into the form below. I'll create a config file and place it in your Do Want! directory.									
								</p>
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
									
									<div id="configResponseSuccess" class="hide alert alert-success">
										The configuration file was written. You're ready for the next step!
									</div>
									<div id="configResponseFailure" class="hide alert alert-error">
										There was a problem writing the configuration file.
									</div>									
									
									<div>
										<button class="btn btn-success" id="checkCredentials">Check These Credentials</button>
										<button class="btn btn-success" id="createConfig">Create Configuration</button>
										<button id="stepTwoNextButton" class="btn btn-primary pull-right nextStepButton disabled" disabled>Next</button>
									</div>									
								</div>
							</form>
						</div>
				  		<div id="step-three" class="item">
							<div class="well">
								<p>Now we need to create the Database tables.</p>
								<div id="tablesResponseSuccess" class="hide alert alert-success">
									
								</div>
								<div id="tablesResponseFailure" class="hide alert alert-error">
									There was a problem setting up tables for Do want.
								</div>	
								<div>
									<button class="btn btn-success" id="createTables">Create Tables</button>
									<button id="stepThreeNextButton" class="btn btn-primary pull-right nextStepButton disabled" disabled>Next</button>
								</div>								
							</div>
				  		</div>
						<div id="step-four" class="item">
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
								</form>
								<div id="userResponseSuccess" class="hide alert alert-success">
									Your user has been created! Please press the next button to complete the setup process.
								</div>
								<div id="userResponseFailure" class="hide alert alert-error">
									There was a problem creating your admin user
								</div>								
								<div>
									<button id="setupAdminUserButton" class="btn btn-success">Create user</button>									
									<button id="stepFourNextButton" class="btn btn-primary pull-right nextStepButton disabled" disabled>Next</button>
								</div>								
							</div>
				  		</div>
				  		<div id="step-five" class="item">
							<div class="well">
								<p>That's it! You're all setup. Click the button below to be taken to your new Do Want installation! 
								</p>
								<div>
									<a id="goToIndexButton" class="btn btn-primary" href="index.php">Go To Do Want!</a>
								</div>																
							</div>
							
				  		</div>

					</div>
				</div>
			</span>
		</div>
	</body>
</html>
