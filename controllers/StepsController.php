<?php

class StepsController{


    public function __construct($models) {
        $this->stepsModel = $models->stepsModel;
        $this->deviceModel = $models->deviceModel;
        $this->userModel = $models->userModel;
    }

    public function read($request) {
        $params = $request->params();
        $this->stepsModel->deviceID = $this->deviceModel->getAssociatedDeviceID($this->userModel->userID);
        
        if(array_key_exists("from", $params) & array_key_exists("to", $params)) {
            $from = $params['from'];
            $to = $params['to'];
            $queryResult = $this->stepsModel->read($from, $to);
            return $queryResult;
        } else {
            $queryResult = $this->stepsModel->read();
            return $queryResult;
        }
    }

    public function create($request) {
        $params = $request->params();

        $this->stepsModel->steps = $params["steps"];
        $this->stepsModel->timeESP = $params["timeESP"];
        $this->stepsModel->deviceID = (array_key_exists("device_id", $params)) ? $params["device_id"] : 0;

        return $this->stepsModel->create();
    }

    public function readAllToday() {
        $totalSteps = $this->stepsModel->getSumToday();
        return $totalSteps;
    }

}