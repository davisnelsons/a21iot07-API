<?php
class Steps{
    
    private $stepsTable = "a21iot07.steps";
    public $steps;
    public $timeESP;
    public $timePHP;
    public $deviceID;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function __call($fname, $args) {
        if($fname == "read") {

            switch (count($args)) {
                case 0:
                    $timeToday = date("Y-m-d ") . "00:00:00";
                    $timeTodayMidnight = date("Y-m-d ") . "23:59:59";
                    return $this->getData($timeToday, $timeTodayMidnight);
                case 2:
                    return $this->getData($args[0], $args[1]);
            }

        }
    }

    private function getData($from, $to) {
        $query = "SELECT DATE_FORMAT(timeESP, '%Y-%m-%d %H:00:00') as `time`, SUM(steps) as `steps` FROM steps
        WHERE timeESP >= :from AND timeESP <= :to
        GROUP BY `time`
        ORDER BY `time`;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":from", $from);
        $stmt->bindParam(":to", $to);
        $stmt->execute();
        $itemCount = $stmt->rowCount();
        if($itemCount > 0){    
            $stepsArray = array();
            $stepsArray["body"] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $b = array(
                    "time" => $time,
                    "steps"=>intval($steps)
                );
                array_push($stepsArray["body"], $b);
            }
            return $stepsArray;
        } else {
            return array("message"=>"no item found");
        }
        
    }


    public function getBestWeek() {
        $query = "SELECT SUM(steps) as sum_steps FROM a21iot07.steps
        GROUP BY WEEK(timeESP)
        ORDER BY sum_steps desc
        LIMIT 1;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        return $sum_steps;
    }

    public function getSumToday() {
        $query = "SELECT SUM(steps) as sum_steps FROM a21iot07.steps
        WHERE timeESP > :date1 AND timeESP < :date2";
        $stmt = $this->conn->prepare($query);
        $dateToday1 = date("Y-m-d ") . "00:00:00";
        $dateToday2 = date("Y-m-d ") . "23:59:59";
        $stmt->bindParam(":date1", $dateToday1);
        $stmt->bindParam(":date2", $dateToday2);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        return $sum_steps;
    }

    public function create() {
        //prepare query
        $query = "INSERT INTO ". $this->stepsTable." (`steps`, `timeESP`, `deviceID`)  VALUES (:steps, :timeESP, :deviceID)";
        $stmt = $this->conn->prepare($query);
        //insert values into ??? fields
        $stmt->bindParam(":steps", $this->steps);
        $stmt->bindParam(":timeESP", $this->timeESP);
        $stmt->bindParam(":deviceID", $this->deviceID);
        
        //execute
        if($stmt->execute()) {
            return true;
        }
        //if there is an error
        $error = $this->conn->errno . ' ' . $this->conn->error;
        return false;   
    }

}