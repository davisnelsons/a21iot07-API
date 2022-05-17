<?php 
class DeviceController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
        $this->deviceModel = new Device($db);
        $this->bpmModel = new Bpm($db);
        $this->stepsModel = new Steps($db);
    }

    public function getDeviceNotifications($request) {
        $this->deviceModel->deviceID = ($request->params())["device_id"];
        $userID = $this->deviceModel->getAssociatedUserID();
        $settings = $this->userModel->getSettings($userID);

        $maxBPM = $settings["max_hr"];
        $stepsGoal = $settings["daily_steps"];

        $lastBPMMeasurement = intval($this->bpmModel->readLast()->bpm);
        $totalStepsMeasurement = $this->stepsModel->getSumToday();

        return(array(
            "max_hr" => ($lastBPMMeasurement >= $maxBPM) ? 1 : 0,
            "daily_steps" => ($totalStepsMeasurement >= $stepsGoal) ? 1 : 0
        ));


    }
}

