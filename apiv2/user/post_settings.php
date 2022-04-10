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


//get posted data
$data = json_decode(file_get_contents("php://input"));
$setting_count = count($data->settings);

for($i = 0; $i < $setting_count; $i++) {
    $setting = $data->settings[$i];
    $status = $user_inst->postSettings($setting->setting, $setting->value, $userID);
    if(!$status) {
        insertFailed();
    }
}



http_response_code(200);
echo json_encode(array("message"=>"insert successful"));

function insertFailed() {
    http_response_code(401);
    echo json_encode(array("error"=>"insert failed"));
    exit();
}

