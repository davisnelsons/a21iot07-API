<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../class/User.php';

$data = json_decode(file_get_contents("php://input"));
$device_id = $data->device_id;


$database = new PDOdb();
$db = $database->getConnection();
$user_inst = new User($db);

//authorize token
$token = get_bearer_token();
$is_jwt_valid = is_jwt_valid($token);
$user_id = get_user_id($token);

if (!$is_jwt_valid) {
    //token invalid, exit script
    token_invalid();
}

if($user_inst->linkDevice($device_id, $user_id)) {
    http_response_code(200);
    echo json_encode(array("message"=>"insert successful"));
} else {
    http_response_code(401);
    echo json_encode(array("error"=>"insert failed"));
}

function token_invalid() {
    http_response_code(401);
    echo json_encode(array("error"=>"invalid token"));
    exit();
}