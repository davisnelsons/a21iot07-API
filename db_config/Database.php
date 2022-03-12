<?php
class Database {
    //config for DB connection
	  private $host  = 'mysql.studev.groept.be';
    private $user  = 'a21iot07';
    private $password   = "TTNkKn1a";
    private $database  = "a21iot07"; 
    
    //connect to db
    public function getConnection(){		
      $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
      if($conn->connect_error){
        die("Error failed to connect to MySQL: " . $conn->connect_error);
      } else {
        return $conn;
      }
    }
}
?>