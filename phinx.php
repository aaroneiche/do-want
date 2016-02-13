<?php
// provides the 

require "config.php";

return array(
    "paths" => array(
        "migrations" => "db/migrations",
        "seeds" => "db/seeds"
    ),
    "environments" => array(
        "default_migration_table" => "phinxlog",
        "default_database" => "production",
        "production" => array(
            "adapter" => "mysql",
            "host" => $dbhost,
            "name" => $dbname,
            "user" => $dbuser,
            "pass" => $dbpass,
            "port" => 3306 //$_ENV['DB_PORT']
        ),
        "development" => array(
            "adapter" => "mysql",
            "host" => $dbhost,
            "name" => $dbname,
            "user" => $dbuser,
            "pass" => $dbpass,
            "port" => 3306 //$_ENV['DB_PORT']
        )        
    )
);
?>