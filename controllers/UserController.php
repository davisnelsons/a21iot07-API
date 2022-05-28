<?php 
class UserController {


    public function __construct($models) {
        $this->userModel = $models->userModel;
        $this->deviceModel = $models->deviceModel;
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

    public function getUserData($request) { //and settings
        $jwt_util = new jwt_util();
        $userData = $this->userModel->getUser($this->userModel->userID);
        $userSettings =  $this->userModel->getSettings($this->userModel->userID);
        return array_merge($userData, $userSettings);
    }

    public function setUserData($request) {
        $userdataArray = json_decode($request->body())->userdata;

        foreach($userdataArray as $userdata) {
            $status = $this->userModel->setUserData($userdata->data, $userdata->value);
        }
        return $status;
    }

    public function setSettings($request) {
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

    public function setFirebaseToken($request) {
        $token = json_decode($request->body())->firebaseToken;
        return $this->userModel->setFirebaseToken($this->userModel->userID, $token);
    }

    public function setUserIDfromDeviceID($deviceID) {
        $this->deviceModel->setDeviceID($deviceID);
        $this->userModel->userID = $this->deviceModel->getAssociatedUserID();
    }


    public function sendNotification($message) {
        $notification = array(
            "title" => $message
        );
        $firebaseToken = $this->userModel->getFirebaseToken();
        firebase_util::sendFirebaseNotification($firebaseToken, $notification);
    }
}