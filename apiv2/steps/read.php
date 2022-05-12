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

if(isset($_GET["from"]) & isset($_GET["to"])) {
    if(validateDate($_GET["from"]) & validateDate($_GET["to"])) {
        $result = $steps_inst->read($_GET["from"], $_GET["to"]);
    } else {
        date_invalid();
    }
} else {
    $result = $steps_inst->read();
}

http_response_code(200);
echo json_encode($result);
