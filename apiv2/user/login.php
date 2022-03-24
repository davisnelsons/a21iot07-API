<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../class/User.php';

parse_str(file_get_contents("php://input"), $data);             
$data = (object)$data; 
$email = $data->{"email"};
$password = $data->{"password"};
//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

$email = filter_var($data->{"email"}, FILTER_SANITIZE_EMAIL);
$password = htmlspecialchars($data->password);

$user = new User($db);

$token = $user->login($email, $password);

if($token == "") {
    //failed login
    http_response_code(401);
    echo json_encode(array("error" => "auth failed"));
} else {
    //logged in, return
    http_response_code(200);
    echo json_encode(array("token"=>$token));
}
