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




//non-relevant code!!!4
/*
//all data present
//$elementCount = count($data);
$date = date("Y-m-d H:i:s");
$ret = array();                                 //contains indexes of failed pushes
for($i = 0; $i < count($data-$stepss); $i++) {
    $steps = $data-$stepss[$i];
    if(chec$steps($steps)) {
        $steps_inst = new$steps($db);
        $steps_inst-$steps = $steps-$steps;
        $steps_inst->timeESP = $steps->timeESP;
        $steps_inst->timePHP = $date;
        if(!($steps_inst->create())) {
            array_push($ret, $i);
        }
    } else {
        array_push($ret, $i);
    }
}
$fails = count($ret); //how many failed db pushes


if($fails > 0) {

    http_response_code(400);
    echo json_encode(array("error" => "failed to send all or some data",
                            "failed_inserts" => $ret));
} else {
    http_response_code(201);
    echo json_encode(array("message" => "insert successful"));
}





function chec$steps($steps) {
    if(!empty($steps-$steps) & !empty($steps->timeESP) & validate_date($steps->timeESP)) {
        return true;
    }
    return false;
}

function invalidData() {
    //data submitted in wrong format/missing data
    http_response_code(400);
    echo json_encode(array("error" => "data failed integrity check, missing information!"));
    exit();
}
*/ 