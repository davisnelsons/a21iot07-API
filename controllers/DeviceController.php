<?php 
class DeviceController {


    public function __construct($models) {
        $this->userModel = $models->userModel;
        $this->deviceModel = $models->deviceModel;
        $this->bpmModel = $models->bpmModel;
        $this->stepsModel = $models->stepsModel;
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

    public function linkDevice($request) {
        $this->deviceModel->deviceID = json_decode($request->body())->deviceID;
        return ($this->deviceModel->linkDevice($this->userModel->userID));
    }
    
}

