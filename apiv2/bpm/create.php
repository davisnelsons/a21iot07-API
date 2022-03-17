<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../class/Bpm.php';
include_once '../../db_config/util.php';


//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

//instantiate new BPM object
$bpm_inst = new Bpm($db);

//get data from the POST request
$data = json_decode(file_get_contents("php://input"));

//check if empty
if(empty($data)) {
    invalidData();
}

//all data present
//$elementCount = count($data);
$date = date("Y-m-d H:i:s");
$ret = array();                                 //contains indexes of failed pushes
for($i = 0; $i < count($data->bpms); $i++) {
    $bpm = $data->bpms[$i];
    if(checkBPM($bpm)) {
        $bpm_inst = new Bpm($db);
        $bpm_inst->bpm = $bpm->bpm;
        $bpm_inst->timeESP = $bpm->timeESP;
        $bpm_inst->timePHP = $date;
        if(!($bpm_inst->create())) {
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





function checkBPM($bpm) {
    if(!empty($bpm->bpm) & !empty($bpm->timeESP) & validate_date($bpm->timeESP)) {
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
