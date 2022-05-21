<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


include_once '../../class/Bpm.php';
include_once '../../db_config/config.php';
include_once '../../db_config/jwt_util.php';
include_once '../../db_config/util.php';

$database = new PDOdb();
$db = $database->getConnection();

$bpm_inst = new Bpm($db);


//authorize token
$token = get_bearer_token();
$is_jwt_valid = is_jwt_valid($token);

if (!$is_jwt_valid) {
    //token invalid, exit script
    token_invalid();
}

//token valid
//check params and determine return mode
if(isset($_GET["last_ESPtime"])) {
    $param_lastESPtime = urldecode($_GET["last_ESPtime"]);
    if(validateDateTime($param_lastESPtime)) {                     //check validity of date
        //return all measurements after this time
        $statement = $bpm_inst->readAfter($param_lastESPtime);
        returnData($statement);
    } else {
        date_invalid();
    }
} else if(isset($_GET["from_ESPtime"]) & isset($_GET["to_ESPtime"])) {
    $param_fromESPtime = urldecode($_GET["from_ESPtime"]);
    $param_toESPtime = urldecode($_GET["to_ESPtime"]);
    if(validateDateTime($param_fromESPtime) & validateDateTime($param_toESPtime)) {   //check validity of dates
        if(isset($_GET["average"])) {
            $statement = $bpm_inst->readBetweenAvg($param_fromESPtime, $param_toESPtime);
            returnAverage($statement);
            exit();
        } else {
            //return all measurements made in this specific period
            $statement=$bpm_inst->readBetween($param_fromESPtime, $param_toESPtime);
            returnData($statement);
        }
    } else {
        date_invalid();
    }
} else if (isset($_GET["get_last"])) {
    //return last measurement
    $statement = $bpm_inst->readLast();
    returnData($statement);
} else if(isset($_GET["week_averages"])) {
    $statement = $bpm_inst->readWeekAvg();
    returnMultipleAverages($statement);
    exit();
} else {
    //return todays measurements
    $statement = $bpm_inst->read();
    returnData($statement);
}


//return requested data
function returnData($statement) { 
    $itemCount = $statement->rowCount();
    if($itemCount > 0){    
        $bpmArray = array();
        $bpmArray["body"] = array();
        $bpmArray["itemCount"] = $itemCount;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $b = array(
                "bpm" => intval($bpm),
                "timeESP"=>$timeESP,
                "timePHP"=>$timePHP
            );
            array_push($bpmArray["body"], $b);
        }
        http_response_code(200);     
        echo json_encode($bpmArray);
        exit();
    }else{   
        http_response_code(200);     
        echo json_encode(
            array("message" => "No item found.")
        );
        exit();

    } 
}


function returnAverage($statement) {
    
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    extract($row);
    if($avg_bpm != null) {
        http_response_code(200);
        echo json_encode(array("avg_bpm" => intval($avg_bpm)));
    } else {
        http_response_code(200);
        echo json_encode(array("avg_bpm" => 0));
    }
}

function returnMultipleAverages($statement) {
    $bpmArray = array();
    $bpmArray["body"] = array();
    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        if($avg_bpm != null) {
            $avg = array(   "avg_bpm"=>intval($avg_bpm),
                            "dateESP"=>$dateESP);
            array_push($bpmArray["body"], $avg);
        }
    }
    http_response_code(200);
    echo json_encode($bpmArray);
}


?>