<?php

class Bpm{
    
    private $bpmTable = "a21iot07.bpm";
    public $bpm;
    public $timeESP;
    public $timePHP;
    public $device_id;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT bpm, timeESP, timePHP FROM a21iot07.bpm
        WHERE timeESP >= :timeToday AND timeESP <= :timeTodayMidnight" ;
        $timeToday = date("Y-m-d ") . "00:00:00";
        $timeTodayMidnight = date("Y-m-d ") . "23:59:59";
        //$stmt = $this->conn->query($query);
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeToday", $timeToday);
        $stmt->bindParam(":timeTodayMidnight", $timeTodayMidnight);
        $stmt->execute();
        $itemCount = $stmt->rowCount();
        if($itemCount > 0){    
            $bpmArray = array();
            $bpmArray["body"] = array();
            $bpmArray["itemCount"] = $itemCount;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $b = array(
                    "bpm" => intval($bpm),
                    "timeESP"=>$timeESP,
                    "timePHP"=>$timePHP
                );
                array_push($bpmArray["body"], $b);
            }
        } else {
            $bpmArray = null;
        }
        return $bpmArray;
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

    public function readBetweenAvg($timeBefore, $timeAfter) {
        $timeBefore = htmlspecialchars(strip_tags($timeBefore));
        $timeAfter = htmlspecialchars(strip_tags($timeAfter));
        $query = "SELECT ROUND(AVG(bpm),0) AS avg_bpm FROM ". $this->bpmTable .
        " WHERE timeESP>:timeBefore AND timeESP < :timeAfter";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeBefore", $timeBefore);
        $stmt->bindParam(":timeAfter", $timeAfter);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        return array("avg_bpm"=>intval($avg_bpm));
    }

    public function readLast() {
        $query = "SELECT * FROM a21iot07.bpm
        WHERE timeESP < :timeToday
        ORDER BY timeESP desc LIMIT 1";
        $timeToday = date("Y-m-d H:i:s");
        //$stmt = $this->conn->query($query);
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeToday", $timeToday);
        $stmt->execute();
        $ret = $stmt->fetch(PDO::FETCH_OBJ);
        return $ret;
    }
    
    public function readWeekAvg() {
        $query = "SELECT ROUND(AVG(bpm)) AS avg_bpm, DATE(timeESP) AS dateESP FROM a21iot07.bpm 
        GROUP BY dateESP
        HAVING dateESP > DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND dateESP <= CURRENT_DATE();";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array(
            "body"=>$ret
        );
    }

    public function create() {

        //prepare query
        $query = "INSERT INTO ". $this->bpmTable." (`bpm`, `timeESP`, `timePHP`, `device_id`)  VALUES (:bpm, :timeESP, :timePHP, :device_id)";
        $stmt = $this->conn->prepare($query);
        //insert values into ??? fields
        $stmt->bindParam(":bpm", $this->bpm);
        $stmt->bindParam(":timeESP", $this->timeESP);
        $stmt->bindParam(":timePHP", $this->timePHP);
        $stmt->bindParam(":device_id", $this->device_id);
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