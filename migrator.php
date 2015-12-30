<html>
<head>
	<style>

	</style>
</head>
<body>
<p>
This is the DoWant Database Migrator. In the future it will be properly integrated into an update page. 
<br/>
For the time being, it's recommended that you simply press "Migrate to this release" on the last item on the list
</p>
<?php
//The interface for Migrating between table structures.
require 'vendor/autoload.php';
require "config.php";

$app = new Phinx\Console\PhinxApplication();
$wrap = new Phinx\Wrapper\TextWrapper($app, array("configuration"=>"phinx_conf.php","parser"=>"php"));

if(isset($_POST['migrate'])){
    
    if($_POST['migrate'] == 'migrate') {
        $command = "getMigrate";
    }else if($_POST['migrate'] == 'rollback'){
        $command = "getRollback";
    }

	$execution = call_user_func([$wrap, $command], 'production', $_POST['migration']);
}

$output = call_user_func([$wrap, 'getStatus'], null, null);

$test = explode("-----\n",$output,2);
$set = explode("\n",$test[1]);

print "<table border=1>";
foreach($set as $k => $migration){
    if(strlen($migration) == 0){
        unset($set[$k]);
    }else{
        //Gets migrations into an array.
        $split = explode("  ",trim($migration),3);
        print "<tr><td>{$split[1]} - {$split[2]}</td><td>";

        $submitType = ($split[0] == 'down') ? "migrate" : "rollback" ;
        if($split[0] == 'down'){
            $submitType = "migrate";
            $label = "Migrate to this release";
        }else{
            $submitType = "rollback";
            $label = "Rollback to this release";
        }


        print "<form method=\"POST\"><input type=\"hidden\" name=\"migration\" value=\"{$split[1]}\"> 
        <input type=\"hidden\" name=\"migrate\" value=\"{$submitType}\" />
        <input type=\"submit\" value=\"{$label}\"/></form></td></tr>";
    }
}
print "</table></form>";

?>
</body>
</html>