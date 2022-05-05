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
        $statement = $steps_inst->read($_GET["from"], $_GET["to"]);
    } else {
        date_invalid();
    }
} else {
    $statement = $steps_inst->read();
}

returnData($statement);


function returnData($statement) { 
    $itemCount = $statement->rowCount();
    if($itemCount > 0){    
        $stepsArray = array();
        $stepsArray["body"] = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $b = array(
                "time" => $time,
                "steps"=>intval($steps)
            );
            array_push($stepsArray["body"], $b);
        }
        http_response_code(200);     
        echo json_encode($stepsArray);
        exit();
    }else{   
        http_response_code(200);     
        echo json_encode(
            array("message" => "No item found.")
        );
        exit();
    } 
}



