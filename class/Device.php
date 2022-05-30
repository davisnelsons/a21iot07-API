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

    public function getAssociatedDeviceID($userID) {
        $query = "SELECT device_id FROM a21iot07.device WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userID);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);
        $deviceID = ($res[0])->device_id;
        $this->deviceID = $deviceID;
        return $deviceID;
    }

    public function linkDevice($userID) {
        $del_query = "DELETE FROM a21iot07.device WHERE user_id = :userID;";
        $stmt = $this->conn->prepare($del_query);
        $stmt->bindParam(":userID", $userID);
        $stmt->execute();
        $query = "INSERT INTO a21iot07.device(`device_id`, `user_id`) VALUES (:deviceID, :userID)
        ON DUPLICATE KEY UPDATE
        user_id = :userID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":deviceID", $this->deviceID);
        $stmt->bindParam(":userID", $userID);
        return $stmt->execute();
    }
    
    public function setDeviceID($deviceID) {
        $this->deviceID = $deviceID;
    }

}