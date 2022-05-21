<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


include_once '../../class/User.php';
include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../db_config/util.php';

$database = new PDOdb();
$db = $database->getConnection();
//init user
$user_inst = new User($db);

//authorize token
$token = get_bearer_token();
$is_jwt_valid = is_jwt_valid($token);

if (!$is_jwt_valid) {
    //token invalid, exit script
    token_invalid();
}

//get user id from the token
$userID = get_user_id($token);

//get statement
$stmt = $user_inst->getSettings($userID);

//initialize array that will be returned
$settings_array = array(
    "daily_steps"=>null,
    "daily_calories"=>null,
    "max_hr"=>null,
    "notify_hr"=>null,
    "notify_sitting"=>null
);


while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $settings_array[$setting] = intval($value);
}

http_response_code(200);
echo json_encode($settings_array);

