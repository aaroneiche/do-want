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
			"base_url" => "http://yourDomainName.tld/hybrid.php",
			"providers" => array(
				"Google" => array(
					"enabled" => true,
					"keys" => array("id" => "yourClientIdGoesHereAbcdefg.apps.googleusercontent.com", 
									"secret" => "ClientSecretGoesHere"),
					"scope" => "profile email"
				),
				"Facebook" => array(
					"enabled" => true,
					"keys" => array("id" => "yourClientIdGoesHereAbcdefg", "secret" => "ClientSecretGoesHere"),
					"trustForwarded" => false
				)
			),
			// If you want to enable logging, set 'debug_mode' to true.
			// You can also set it to
			// - "error" To log only error messages. Useful in production
			// - "info" To log info and error messages (ignore debug messages)
			"debug_mode" => false,
			// Path to file writable by the web server. Required if 'debug_mode' is not false
			"debug_file" => "logs/error_log",
);
