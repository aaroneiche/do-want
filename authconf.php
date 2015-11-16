<?php

/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */
// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return
		array(
			"base_url" => "http://aaron.dev/hybrid.php",
			"providers" => array(
				"Google" => array(
					"enabled" => true,
					"keys" => array("id" => "911019304085-0faeouu5lmnis90mlbukj78dogu9bcfs.apps.googleusercontent.com", 
									"secret" => "J9cTY87gsnZlJNn74iz1Or-J"),
					"scope" => "https://www.googleapis.com/auth/userinfo.profile"
				),
				"Facebook" => array(
					"enabled" => true,
					"keys" => array("id" => "31003322366", "secret" => "c5fce8a14f1d7e01b4c2b7816f929344"),
					"trustForwarded" => false
				)
			),
			// If you want to enable logging, set 'debug_mode' to true.
			// You can also set it to
			// - "error" To log only error messages. Useful in production
			// - "info" To log info and error messages (ignore debug messages)
			"debug_mode" => false,
			// Path to file writable by the web server. Required if 'debug_mode' is not false
			"debug_file" => "",
);
