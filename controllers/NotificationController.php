<?php 
class NotificationController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
    }

    public function sendNotification($message) {
        $notification = array(
            "title" => "Congratulations, step goal reached!"
        );
        $firebaseToken = $this->userModel->getFirebaseToken();
        sendFirebaseNotification($firebaseToken, $notification);
    }