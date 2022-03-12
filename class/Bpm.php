<?php
require "../../db_config/config.php";
class Bpm{
    
    private $bpmTable = "a21iot07.bpm";
    public $bpm;
    public $timeESP;
    public $timePHP;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->bpmTable.";");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    public function readPDO() {
        $query = "SELECT * FROM a21iot07.bpm";
        $statement = $this->conn->query($query);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = json_encode($result);
        return $result;
    }

    public function readAfter($timeESP) {
        $timeESP = htmlspecialchars(strip_tags($timeESP));
        $timeESP = str_replace(": ", ":", $timeESP);
        $query = "SELECT * FROM ". $this->bpmTable .
        " WHERE timeESP>? ";
        $stmt = $this->conn->prepare($query);
        //echo $query;
        if($stmt) {
            $stmt->bind_param("s", $timeESP);
            $result = $stmt->execute();
            if($result != false) {
                return $result;
            } else {
                echo "result false";
                return false;
            }
        } else {
            
            $error = $this->conn->errno . ' ' . $this->conn->error;
            echo $error;
            return false;
        }


        
        
        $result = $stmt->get_result();
        return $result;
    }

    

    public function create() {
        $query = "INSERT INTO ". $this->bpmTable." (`bpm`, `timeESP`, `timePHP`)  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        //insert values into ??? fields
        if($stmt) {
            $stmt->bind_param("iss", $this->bpm, $this->timeESP, $this->timePHP);
            //execute
            if($stmt->execute()) {
                return true;
            }
            $error = $this->conn->errno . ' ' . $this->conn->error;
            echo $error;
            return false;   
        } else {
            $error = $this->conn->errno . ' ' . $this->conn->error;
            echo $error;
            return false;
        }
            
    }

}