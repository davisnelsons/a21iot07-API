<?php


class PDOdb {

    
    private $host = '35.205.31.15';
    private $db = 'a21iot07';
    private $user = 'root';
    private $password = 'secret';

    public function getConnection() {
        $dsn = "mysql:host=" . $this->host .";dbname=".$this->db.";charset=UTF8";
        $pdo = new PDO($dsn, $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
?>
