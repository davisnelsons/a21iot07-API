<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once '../../db_config/config.php';
include_once '../../class/Steps.php';
include_once '../../db_config/util.php';
include_once '../../class/User.php';
include_once "../../db_config/firebase_util.php";
//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

//instantiate new$steps object
$steps_inst = new Steps($db);

//for now get data from the link
$steps = $_REQUEST["steps"];
$timeESP = $_REQUEST["timeESP"];

if(isset($_REQUEST["device_id"])) {
    $steps_inst->device_id = intval($_REQUEST["device_id"]);
} else {
    $steps_inst->device_id = 0;
}

if(isset($steps) & isset($timeESP)) {
    $steps_inst->steps = $steps;
    $steps_inst->timeESP = $timeESP;
    if(!$steps_inst->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "insert successful"));
    } else {
        http_response_code(201);
        echo json_encode(array("message" => "insert successful"));
    }
} else {
    exit();
} 

$device_id = $steps_inst->device_id;
$user_inst = new User($db);
$user_id = $user_inst->getUserIDfromDeviceID($device_id);
$stmt = $user_inst->getSettings($user_id);
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$steps_goal = $settings["daily_steps"];
$steps_made = $steps_inst->getSumToday();
if($steps_made >= $steps_goal & $steps_made-$steps <= $steps_goal) {
    $notification = array(
        "title"=> "Congratulations, you have reached your daily step goal!"
    );
    sendFirebaseNotification($user_inst->getFirebaseToken($user_id), $notification);
}

exit();