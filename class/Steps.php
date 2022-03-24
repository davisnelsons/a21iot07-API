<?php
class Steps{
    
    private $stepsTable = "a21iot07.steps";
    public $bpm;
    public $timeESP;
    public $timePHP;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM". $this->stepsTable ."
        WHERE timeESP >= :timeToday" ;
        $timeToday = date("Y-m-d ") . "00:00:00";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":timeToday", $timeToday);
        $stmt->execute();
        return $stmt;
    }



}