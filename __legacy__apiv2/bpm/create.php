<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../class/Bpm.php';
include_once '../../class/User.php';
include_once '../../db_config/util.php';
include_once "../../db_config/firebase_util.php";

//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

//instantiate new BPM object
$bpm_inst = new Bpm($db);
/*
//get data from the POST request
$data = json_decode(file_get_contents("php://input"));

//check if empty
if(empty($data)) {
    invalidData();
}*/

//for now get data from the link
$bpm = $_REQUEST["bpm"];
$timeESP = $_REQUEST["timeESP"];

if(isset($_REQUEST["device_id"])) {
    $bpm_inst->device_id = intval($_REQUEST["device_id"]);
} else {
    $bpm_inst->device_id = 0;
}

if(isset($bpm) & isset($timeESP)) {
    $bpm_inst->bpm = $bpm;
    $bpm_inst->timeESP = $timeESP;
    $bpm_inst->timePHP = date("Y-m-d H:i:s");
    if(!$bpm_inst->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "insert successful"));
    } else {
        http_response_code(201);
        echo json_encode(array("message" => "insert successful"));
    }
}

//check for high bpm, notify if necessary
if(isset($_REQUEST["device_id"])) {
    $device_id = $_REQUEST["device_id"];
    $user_inst = new User($db);
    $user_id = $user_inst->getUserIDfromDeviceID($device_id);
    $stmt = $user_inst->getSettings($user_id);
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $max_hr = $settings["max_hr"];
    if($bpm > $max_hr) {
        $notification = array(
            "title"=> "Watch out, maximum heart rate reached!"
        );
        sendFirebaseNotification($user_inst->getFirebaseToken($user_id), $notification);
    }
}


exit();




//non-relevant code!!!4
/*
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
*/ 