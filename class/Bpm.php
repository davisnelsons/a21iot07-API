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
        $query = "SELECT * FROM a21iot07.bpm
        WHERE timeESP >= :timeToday" ;
        $timeToday = date("Y-m-d ") . "00:00:00";
        //$stmt = $this->conn->query($query);
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeToday", $timeToday);
        $stmt->execute();
        return $stmt;
    }

    public function readAfter($timeESP) {
        $timeESP = htmlspecialchars(strip_tags($timeESP));
        $query = "SELECT * FROM ". $this->bpmTable .
        " WHERE timeESP>:timeESP ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeESP", $timeESP);
        $stmt->execute();
        return $stmt;
    }
        
    public function readBetween($timeBefore, $timeAfter) {
        $timeBefore = htmlspecialchars(strip_tags($timeBefore));
        $timeAfter = htmlspecialchars(strip_tags($timeAfter));
        $query = "SELECT * FROM ". $this->bpmTable .
        " WHERE timeESP>:timeBefore AND timeESP < :timeAfter";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeBefore", $timeBefore);
        $stmt->bindParam(":timeAfter", $timeAfter);
        $stmt->execute();
        return $stmt;
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