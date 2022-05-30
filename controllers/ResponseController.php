<?php
class ResponseController {

    public function __construct($response) {
        $this->response = $response;
    }

    public function respond($data) {
        $this->response->json($data);
        $this->response->lock();
    }

    public function customMessage($message) {
        $this->response->json(array(
            "message"=>$message
        ));
        $this->response->lock();
    }

    public function insertSuccessful() {
        $this->customMessage("insert successful");
    }

    public function insertFailed() {
        $this->response->json(array(
            "error"=>"insert failed"
        ));
        $this->response->lock();
    }

    public function invalidToken() {
        $this->response->json(array(
            "error"=>"invalid token"
        ));
        $this->response->lock();
    }

    public function noDevice() {
        $this->response->json(array(
            "error"=>"no linked device"
        ));
        $this->response->lock();
    }


}