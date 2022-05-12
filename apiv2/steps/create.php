<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../class/Steps.php';
include_once '../../db_config/util.php';


//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

//instantiate new$steps object
$steps_inst = new Steps($db);
/*
//get data from the POST request
$data = json_decode(file_get_contents("php://input"));

//check if empty
if(empty($data)) {
    invalidData();
}*/

//for now get data from the link
$steps = $_REQUEST["steps"];
$timeESP = $_REQUEST["timeESP"];



if(isset($_REQUEST["deviceID"])) {
    $steps_inst->deviceID = intval($_REQUEST["deviceID"]);
} else {
    $steps_inst->deviceID = 0;
}


if(isset($steps) & isset($timeESP)) {
    $steps_inst->steps = $steps;
    $steps_inst->timeESP = $timeESP;
    //$steps_inst->timePHP = date("Y-m-d H:i:s");
    if(!$steps_inst->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "insert successful"));
    } else {
        http_response_code(201);
        echo json_encode(array("message" => "insert successful"));
    }
} 


exit();
