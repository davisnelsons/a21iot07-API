<?php 
class UserController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
    }

    public function login($request) {
        $body = json_decode($request->body());
        $expires_in = 3600;
        $token = $this->userModel->login($body->email, $body->password, $expires_in);
        return array(
            "token"=>$token,
            "expires_on"=>time()+$expires_in
        );
    }

    public function authorizeUser($request) {
        $jwt_util = new jwt_util();  
        $token = explode(" ", $request->headers()->Authorization)[1];
        $tokenValid = $jwt_util->is_jwt_valid($token);
        if($tokenValid) {
            $this->userModel->setUserID($jwt_util->get_user_id($token));
        }
        return $tokenValid;
    }

    public function getUserData($request) {
        $jwt_util = new jwt_util();
        $userData = $this->userModel->getUser($this->userModel->userID);
        $userSettings =  $this->userModel->getSettings($this->userModel->userID);
        return json_encode(array_merge($userData, $userSettings));
    }

    public function setUserData($request) {
        $body = json_decode($request->body());
        $settingsArray = $body->settings;
        foreach($settingsArray as $setting) {
            $status = $this->userModel->postSettings(
                $setting->setting,
                $setting->value,
                $this->userModel->userID
            );
        }
        return $status;
    }
}