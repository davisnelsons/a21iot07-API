<?php
//require "../../db_config/config.php";
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
        $query = "SELECT * FROM a21iot07.bpm";
        $stmt = $this->conn->query($query);
        $stmt->execute();
        return $stmt;
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

        //prepare query
        $query = "INSERT INTO ". $this->bpmTable." (`bpm`, `timeESP`, `timePHP`)  VALUES (:bpm, :timeESP, :timePHP)";
        $stmt = $this->conn->prepare($query);
        //insert values into ??? fields
        $stmt->bindParam(":bpm", $this->bpm);
        $stmt->bindParam(":timeESP", $this->timeESP);
        $stmt->bindParam(":timePHP", $this->timePHP);
        
        //execute
        if($stmt->execute()) {
            return true;
        }

        //if there is an error
        $error = $this->conn->errno . ' ' . $this->conn->error;
        echo $error;
        return false;   
       
            
    }

}