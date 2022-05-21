<?php
class User{
    private $userTable = "a21iot07.user";
    public $userFirstName;
    public $userLastName;
    public $userEmail;
    public $userID;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function signup($newFirstName, $newLastName, $newEmail, $newPassword, $newBirthDate, $newWeight, $newHeight) {
        $query = "INSERT INTO ".$this->userTable." (`name`, `lastName`, `email`, `password`, `birthDate`, `weight`, `height`)
         VALUES (:firstName, :lastName, :email, :password, :birthDate, :weight, :height)";
        $query2 = "SELECT userId FROM ".$this->userTable." WHERE email like :email;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":firstName", $newFirstName);
        $stmt->bindParam(":lastName", $newLastName);
        $stmt->bindParam(":email", $newEmail);
        $stmt->bindParam(":password", $newPassword);
        $stmt->bindParam(":birthDate", $newBirthDate);
        $stmt->bindParam(":weight", $newWeight);
        $stmt->bindParam(":height", $newHeight);
        //execute
        if($stmt->execute()) {
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":email", $newEmail);
            $stmt2->execute();
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $this->postDefaultSettings($userId);
            return true;
        }
        //if there is an error
        $error = $this->conn->getMessage();
        echo $error;
        return false;   
    }

    public function login($email, $password, $expires_in) {
        $query = "SELECT userId FROM ".$this->userTable." WHERE email LIKE :email AND password like :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->execute();
        if($stmt->rowCount() == 1) {
            //generate JWT token
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);                                                    //makes a variable %$userId containing userId
            $headers = array("alg"=>"HS256", "typ"=>"JWT");
            $payload = array("userId"=>$userId, "exp"=>(time()+$expires_in));        //token expires in 1 hour, token also contains userId
            $jwt_util = new jwt_util();
            $jwt = $jwt_util->generate_jwt($headers, $payload);
            return $jwt;
        } else {
            //failed to authorize
            return "";
        }
    }

    public function getUser($user_id) {
        $query = "SELECT name, lastName, birthDate, weight, height, email FROM a21iot07.user WHERE userId = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam("user_id", $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $user_data = array(
            "name" => $name,
            "lastName" => $lastName,
            "birthDate" => $birthDate,
            "weight" => $weight,
            "height" => $height,
            "email" => $email,
            "best_step_week" => $best_steps
        );
        return $user_data;
    }

    

    public function setUserID($userID) {
        $this->userID = $userID;
    }

    public function linkDevice($device_id, $user_id) {
        $query = "INSERT INTO a21iot07.Device (device_id, user_id) VALUES (:device_id, :user_id);";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":device_id", $device_id);
        $stmt->bindParam(":user_id", $user_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getSettings($user_id) {
        $query = "SELECT setting, value FROM a21iot07.user_settings WHERE user_id = :user_id;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $settings_array = array(
            "daily_steps"=>null,
            "daily_calories"=>null,
            "max_hr"=>null,
            "notify_hr"=>null,
            "notify_sitting"=>null
        );
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $settings_array[$setting] = intval($value);
        }
        return $settings_array;
    }

    public function postSettings($setting, $value, $user_id) {
        $query = "INSERT INTO a21iot07.user_settings(setting, value, user_id) 
        VALUES(:setting, :value, :user_id)
        ON DUPLICATE KEY UPDATE
        value = :value;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":setting", $setting);
        $stmt->bindParam(":value", $value);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function postDefaultSettings($user_id) {
        if( $this->postSettings("daily_steps", 10000, $user_id) &
            $this->postSettings("daily_calories", 400, $user_id) &
            $this->postSettings("max_hr", 190, $user_id) )
        {
            return true;
        }
        return false;
    
    }

    public function getFirebaseToken($user_id) {
        $query =  "SELECT firebaseToken FROM a21iot07.user
                    WHERE userId = :userID;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userID", $user_id);
        $stmt->execute();
        $ret = $stmt->fetch(PDO::FETCH_OBJ);
        $token = $ret->firebaseToken;
        return $token;
    }
    
    public function setFirebaseToken($user_id, $token) {
        $query =  "UPDATE a21iot07.user SET
        firebaseToken = :token
        WHERE userId = :userID;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userID", $user_id);
        $stmt->bindParam(":token", $token);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}