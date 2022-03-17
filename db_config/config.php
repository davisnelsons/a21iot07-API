<?php


class PDOdb {

    private $host = 'mysql.studev.groept.be';
    private $db = 'a21iot07';
    private $user = 'a21iot07';
    private $password = 'TTNkKn1a';

    public function getConnection() {
        $dsn = "mysql:host=" . $this->host .";dbname=".$this->db.";charset=UTF8";
        $pdo = new PDO($dsn, $this->user, $this->password);
        return $pdo;
    }
}
?>