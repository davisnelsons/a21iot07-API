<?php
class Steps{
    
    private $stepsTable = "a21iot07.steps";
    public $steps;
    public $timeESP;
    public $timePHP;
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
        return $stmt;
    }


    public function getBestWeek() {
        $query = "SELECT SUM(steps) as sum_steps FROM a21iot07.steps
        GROUP BY WEEK(timeESP)
        ORDER BY sum_steps desc
        LIMIT 1;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        //prepare query
        $query = "INSERT INTO ". $this->stepsTable." (`steps`, `timeESP`)  VALUES (:steps, :timeESP)";
        $stmt = $this->conn->prepare($query);
        //insert values into ??? fields
        $stmt->bindParam(":steps", $this->steps);
        $stmt->bindParam(":timeESP", $this->timeESP);
        //$stmt->bindParam(":timePHP", $this->timePHP);
        
        //execute
        if($stmt->execute()) {
            return true;
        }
        //if there is an error
        $error = $this->conn->errno . ' ' . $this->conn->error;
        return false;   
    }

}