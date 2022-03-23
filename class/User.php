<?php
class User{
    private $userTable = "a21iot07.user";
    public $userFirstName;
    public $userLastName;
    public $userEmail;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function signup($newFirstName, $newLastName, $newEmail, $newPassword, $newBirthDate, $newWeight, $newHeight) {
        $query = "INSERT INTO ".$this->userTable." (`name`, `lastName`, `email`, `password`, `birthDate`, `weight`, `height`)
         VALUES (:firstName, :lastName, :email, :password, :birthDate, :weight, :height)";
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
            return true;
        }
        //if there is an error
        $error = $this->conn->getMessage();
        echo $error;
        return false;   
    }

    public function login($email, $password) {
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
            $payload = array("userId"=>$userId, "exp"=>(time()+3600));        //token expires in one hour, token also contains userId
            $jwt = generate_jwt($headers, $payload);
            return $jwt;
        } else {
            //failed to authorize
            return "";
        }
    }

}