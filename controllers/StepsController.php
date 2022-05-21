<?php

class StepsController{


    public function __construct($models) {
        $this->stepsModel = $models->stepsModel;
    }

    public function read($request) {
        $params = $request->params();

        if(array_key_exists("from", $params) & array_key_exists("to", $params)) {
            $from = $params['from'];
            $to = $params['to'];
            $queryResult = $this->stepsModel->read($from, $to);
            return json_encode($queryResult);
        } else {
            $queryResult = $this->stepsModel->read();
            return json_encode($queryResult);
        }
    }

    public function create($request) {
        $params = $request->params();

        $this->stepsModel->steps = $params["steps"];
        $this->stepsModel->timeESP = $params["timeESP"];
        $this->stepsModel->device_id = (array_key_exists("device_id", $params)) ? $params["device_id"] : 0;

        return $this->stepsModel->create();
    }

    public function readAllToday() {
        $totalSteps = $this->stepsModel->getSumToday();
        return $totalSteps;
    }

}