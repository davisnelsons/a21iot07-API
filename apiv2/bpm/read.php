<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../db_config/Database.php';
include_once '../../class/Bpm.php';
include_once '../../db_config/config.php';

$database = new PDOdb();
$db = $database->getConnection();

$bpm_inst = new Bpm($db);

$statement = $bpm_inst->read();
$itemCount = $statement->rowCount();

if($itemCount > 0){    

    $bpmArray = array();
    $bpmArray["body"] = array();
    $bpmArray["itemCount"] = $itemCount;
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $b = array(
            "bpm" => $bpm,
            "timeESP"=>$timeESP,
            "timePHP"=>$timePHP
        );
        array_push($bpmArray["body"], $b);
    }
    
    http_response_code(200);     
    echo json_encode($bpmArray);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 

?>