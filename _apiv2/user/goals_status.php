<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


include_once '../../class/User.php';
include_once '../../class/Steps.php';
include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../db_config/util.php';

$database = new PDOdb();

$db = $database->getConnection();
//init user
$user_inst = new User($db);
$steps_inst = new Steps($db);

//authorize token
$token = get_bearer_token();
$is_jwt_valid = is_jwt_valid($token);

if (!$is_jwt_valid) {
    if(isset($_REQUEST["device_id"])) {
        $userID = $user_inst->getUserIDfromDeviceID($_REQUEST["device_id"]);;
    } else {
        //token invalid, exit script
        token_invalid();
    }
} else {
    $userID = get_user_id($token);
}

//get user id from the token


//first get user settings
$stmt = $user_inst->getSettings($userID);

//encode key val
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    //init result array
$result = array();


if(array_key_exists("daily_steps",  $settings)) {
    $steps_today = $steps_inst->getSumToday();
    if($steps_today >= $settings['daily_steps']) {
        $result["daily_steps"] = 1;
    } else {
        $result["daily_steps"] = 0;
    }
}

http_response_code(200);
echo json_encode($result);