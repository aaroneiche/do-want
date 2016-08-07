<?php
header("Access-Control-Allow-Origin: *");

require "../vendor/autoload.php";
require "../db_objects.php"; // Class definitions.

// Create and configure Slim app
$app = new \Slim\App;

// Define app routes
$app->get('/update', function ($request, $response, $args) {
    return $response->write("Run Update method");
});

$app->get('/user/{userid}/items', function($req, $res, $args) {
  $listItems = Model::factory('Items')->where('userid',$args['userid'])->findArray();
  if($listItems !== false){
    $res->withJSON($listItems);
  }else{
    $res->withJSON(array("message"=>"failure"));
  }
});

// Run app
$app->run();
?>
