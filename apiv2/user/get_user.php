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
    //token invalid, exit script
    token_invalid();
}

//get user id from the token
$userID = get_user_id($token);
$stmt = $user_inst->getUser($userID);
$stmt_steps = $steps_inst->getBestWeek();

$itemCount = $stmt->rowCount();
if($itemCount == 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    $user_data = array(
        "name" => $name,
        "lastName" => $lastName,
        "birthDate" => $birthDate,
        "weight" => $weight,
        "height" => $height,
        "email" => $email
    );
    http_response_code(200);
    echo json_encode($user_data);
} else {
    http_response_code(401);
    echo json_encode(array("error"=>"failed"));
}