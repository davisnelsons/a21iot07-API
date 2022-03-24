<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../class/Bpm.php';
include_once '../../db_config/config.php';

$database = new PDOdb();
$db = $database->getConnection();

$bpm_inst = new Bpm($db);


//check params and determine return mode
if(isset($_GET["last_ESPtime"])) {
    $param_lastESPtime = urldecode($_GET["last_ESPtime"]);
    //return all measurements after this time
    $statement = $bpm_inst->readAfter($param_lastESPtime);

} else if(isset($_GET["from_ESPtime"]) & isset($_GET["to_ESPtime"])) {
    $param_fromESPtime = urldecode($_GET["from_ESPtime"]);
    $param_toESPtime = urldecode($_GET["to_ESPtime"]);
    //return all measurements made in this specific period
    $statement=$bpm_inst->readBetween($param_fromESPtime, $param_toESPtime);
} else {
    //return todays measurements
    $statement = $bpm_inst->read();
}



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
    http_response_code(200);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 

?>