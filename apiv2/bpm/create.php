<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../class/Bpm.php';


//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

//instantiate new BPM object
$bpm = new Bpm($db);

//get data from the POST request
$data = json_decode(file_get_contents("php://input"));

//TODO: multiple measurements at once
//check if empty
if(!empty($data->bpm) && !empty($data->timeESP)) {
    //all data present
    $bpm->bpm = $data->bpm;
    $bpm->timeESP = $data->timeESP;
    //time when received
    $bpm->timePHP=date("Y-m-d H:i:s");

    if($bpm->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "done"));
    } else {
        http_response_code(503);
        echo json_encode(array("error" => "unable to send data to server"));
    }
 
} else {
    http_response_code(400);
    echo json_encode(array("error" => "data failed integrity check, missing information!"));
}







