<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../class/User.php';

$expires_in = 3600; //expiry time in seconds
          
$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$password = $data->password;
//connect to DB
$database = new PDOdb();
$db = $database->getConnection();

$email = filter_var($data->{"email"}, FILTER_SANITIZE_EMAIL);
$password = htmlspecialchars($data->password);

$user = new User($db);

$token = $user->login($email, $password, $expires_in);

if($token == "") {
    //failed login
    http_response_code(401);
    echo json_encode(array("error" => "auth failed"));
} else {
    //logged in
    http_response_code(200);
    echo json_encode(array("token"=>$token,
                            "expires_on"=>time()+$expires_in));
}
