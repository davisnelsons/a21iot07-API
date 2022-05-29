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
        $this->bpmModel->deviceID = ($request->params())["device_id"];
        $this->stepsModel->deviceID = ($request->params())["device_id"];
        $userID = $this->deviceModel->getAssociatedUserID();
        $settings = $this->userModel->getSettings($userID);

        $maxBPM = $settings["max_hr"];
        $stepsGoal = $settings["daily_steps"];

        $lastBPMMeasurement = intval($this->bpmModel->readLast()->bpm);
        $totalStepsMeasurement = $this->stepsModel->getSumToday();
        // error_log("lastbpm", 0);
        // error_log($lastBPMMeasurement, 0);
        // error_log("totalsteps", 0);
        // error_log($totalStepsMeasurement, 0);
        // error_log("maxhr", 0);
        // error_log($maxBPM, 0);
        // error_log("dailysteps", 0);
        // error_log($stepsGoal, 0);

        // error_log(json_encode(
        //     array(
        //         "max_hr" => ($lastBPMMeasurement >= $maxBPM) ? 1 : 0,
        //         "daily_steps" => ($totalStepsMeasurement >= $stepsGoal) ? 1 : 0
        //     )
        //     ));
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

