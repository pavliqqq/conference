<?php

namespace database;


use PDO;
use PDOException;

class Database
{
    private PDO $conn;
    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';

        $db = $config['db'];

        $host = $db['host'];
        $dbname = $db['dbname'];
        $user = $db['user'];
        $password = $db['password'];

        try {
            $this->conn = new PDO("mysql:host=" . $host . ";dbname=" . $dbname . ";user=" . $user . ";password=" . $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    function query(string $sql,array $params = []){
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }
}