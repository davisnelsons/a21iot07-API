<?php

class BpmController{

    public function __construct($models) {
        $this->bpmModel = $models->bpmModel;
        $this->deviceModel = $models->deviceModel;
        $this->userModel = $models->userModel;
    }

    public function read($request) {
        $params = $request->params();
        
        //check for specifiers
        if(array_key_exists("get_last", $params) || ($params["specifier"] == "get_last")) {
            $specifier = "get_last";
        } else if (array_key_exists("week_averages", $params)) {
            $specifier = "week_averages";
        } else if (array_key_exists("minmax", $params)) {
            $specifier = "minmax";
        } else {
            $specifier = "today";
        }

        //translate from user id to device id
        $this->bpmModel->deviceID = $this->deviceModel->getAssociatedDeviceID($this->userModel->userID);
        $average = array_key_exists("average", $params);
        $from = array_key_exists("from_ESPtime", $params) ? $params["from_ESPtime"] : null;
        $to = array_key_exists("to_ESPtime", $params) ? $params["to_ESPtime"] : null;
        
        if($average) {
            return json_encode($this->bpmModel->readBetweenAvg($from, $to));
        }
        switch($specifier) {
            case "today": 
                $queryResult = $this->bpmModel->read();
                return json_encode($queryResult);
                break;
            case "get_last":
                $queryResult = $this->bpmModel->readLast();
                return (json_encode(array(
                    "bpm"=>(intval($queryResult->bpm)),
                    "timeESP"=>($queryResult->timeESP)
                )));
                break;
            case "week_averages":
                $queryResult = $this->bpmModel->readWeekAvg();
                return json_encode($queryResult);
                break;
            case "minmax":
                $min = $this->bpmModel->readWeekMin();
                $max = $this->bpmModel->readWeekMax();
                return json_encode(array(
                    "min"=>$min,
                    "max"=>$max   
                )); 

        }
    }

    public function create($request) {
        $params = $request->params();
        $this->bpmModel->bpm = $params["bpm"];
        $this->bpmModel->timeESP = $params["timeESP"];
        $this->bpmModel->device_id = (array_key_exists("device_id", $params)) ? $params["device_id"] : 0;
        $this->bpmModel->timePHP = date("Y-m-d H:i:s");

        return $this->bpmModel->create();
    }

    public function readLast() {
        return $this->read(array("specifier" => "get_last"));   
    }

}