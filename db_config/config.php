<?php


class PDOdb {

    
    private $host = '---';
    private $db = '---';
    private $user = '---';
    private $password = '---';

    public function getConnection() {
        $dsn = "mysql:host=" . $this->host .";dbname=".$this->db.";charset=UTF8";
        $pdo = new PDO($dsn, $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
?>
