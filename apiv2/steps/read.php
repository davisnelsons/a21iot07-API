<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../class/Steps.php';
include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../db_config/util.php';

$database = new PDOdb();
$db = $database->getConnection();

$steps_inst = new Steps($db);
$token = get_bearer_token();
$is_jwt_valid = is_jwt_valid($token);

if(!$is_jwt_valid) {
    token_invalid();
}



$statement = $steps_inst->read();






function token_invalid() {
    http_response_code(401);
    echo json_encode(array("error"=>"invalid token"));
    exit();
}

function date_invalid() {
    http_response_code(400);
    echo json_encode(array("error" => "invalid date format"));
    exit();
}
