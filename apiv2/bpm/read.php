<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../db_config/Database.php';
include_once '../../class/Bpm.php';
include_once '../../db_config/config.php';

$database = new PDOdb();
$db = $database->getConnection();

$bpm = new Bpm($db);

//$timeESPAfter = $_GET['timeESPAfter'];
/*
if ($timeESPAfter != null) {
    echo "readAfter!";
    $result = $bpm->readAfter($timeESPAfter);
} else {
    
}*/
$result = $bpm->readPDO();

echo $result;
if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["bpms"]=array(); 
	while ($item = $result->fetch_assoc()) { 	
        extract($item); 
        $itemDetails=array(
            "bpm" => $bpm,
            "timeESP" => $timeESP,
            "timePHP" => $timePHP		
        ); 
       array_push($itemRecords["bpms"], $itemDetails);
    }    
    http_response_code(200);     
    echo json_encode($itemRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 

?>