<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../class/User.php';



//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

//get data
$data = json_decode(file_get_contents("php://input"));
//sanitize
$firstName = htmlspecialchars($data->firstName);
$lastName = htmlspecialchars($data->lastName);
if(filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
} else {
    failure();
}
$password = htmlspecialchars($data->password);
$birthDate = htmlspecialchars($data->birthDate);
$weight = htmlspecialchars($data->weight);
$height = htmlspecialchars($data->height);


//init user
$user = new User($db);

if($user->signup($firstName, $lastName, $email, $password, $birthDate, $weight, $height)) {
    //all fine
    http_response_code(201);
    echo json_encode(array("message"=>"done"));
} else {
    //sign up has failed
    failure();
}

function failure() {
    http_response_code(400);
    echo json_encode(array("error"=>"failed to sign up user"));
    exit();
}
