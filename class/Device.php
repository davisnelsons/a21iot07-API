<?php
class Device{
    public $deviceID;
    private $conn; 

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAssociatedUserID() {
        $query = "SELECT user_id FROM a21iot07.device WHERE device_id = :device_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":device_id", $this->deviceID);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);
        $userID = ($res[0])->user_id;
        return $userID;
    }

}