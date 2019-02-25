<?php

class Database {
    
    private $host = "localhost";
    private $database = "assignments";
    
    // TODO add regular user to mysql
    private $username = "root";
    private $password = "";
    
    public $connection;
    
    public function getConnection() {
        
        $this -> connection = null;
        
        try {
            
            $this -> connection = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
        } catch (PDOException $ex) {
            echo "Connection error: " . $ex->getMessage();
        }
        return $this->connection;
    }
}