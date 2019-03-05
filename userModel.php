<?php

use \Firebase\JWT\JWT;

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
include_once './models/tokenModel.php';

class User {

    private $connection;
    public $table = "user";
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $confirmPassword;

    public function __construct($db) {
        $this->connection = $db;
    }

    public function create() {


        $this->firstName = !empty($this->firstName) ? filter_var($this->firstName, FILTER_SANITIZE_STRING) : null;
        $this->lastName = !empty($this->lastName) ? filter_var($this->lastName, FILTER_SANITIZE_STRING) : null;
        $this->email = !empty($this->email) ? filter_var($this->email, FILTER_VALIDATE_EMAIL) : null;

        if ($this->password === $this->confirmPassword) {

            $this->password = password_hash($this->password, PASSWORD_DEFAULT);

            $query = "INSERT INTO " . $this->table . " (FIRSTNAME, LASTNAME, EMAIL, PASSWORD) VALUES (?, ?, ?, ?);";
            $stmt = $this->connection->prepare($query);
            $params = array($this->firstName, $this->lastName, $this->email, $this->password);

            if ($stmt->execute($params)) {

                return true;
            }
        }
        return false;
    }

    public function read() {

        $this->email = !empty($this->email) ? filter_var($this->email, FILTER_SANITIZE_EMAIL) : null;
        $query = "SELECT ID, FIRSTNAME, LASTNAME, PASSWORD FROM " . $this->table . " WHERE email = ?;";
        $stmt = $this->connection->prepare($query);
        $stmt->execute(array($this->email));

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['ID'];
            $this->firstName = $row['FIRSTNAME'];
            $this->lastName = $row['LASTNAME'];
            $this->password = $row['PASSWORD'];

            return true;
        }
        return false;
    }

    public function update() {

        $this->id = !empty($this->id) ? filter_var($this->id, FILTER_SANITIZE_NUMBER_INT) : null;
        $this->firstName = !empty($this->firstName) ? filter_var($this->firstName, FILTER_SANITIZE_STRING) : null;
        $this->lastName = !empty($this->lastName) ? filter_var($this->lastName, FILTER_SANITIZE_STRING) : null;
        $this->email = !empty($this->email) ? filter_var($this->email, FILTER_SANITIZE_EMAIL) : null;
        $this->password = !empty($this->password) ? filter_var($this->password, FILTER_SANITIZE_STRING) : null;

        if ($this->password === $this->confirmPassword) {

            $query = "UPDATE " . $this->table . " SET FIRSTNAME=?, LASTNAME=?, EMAIL=?, PASSWORD=? WHERE ID=?;";
            $stmt = $this->connection->prepare($query);
            $params = array($this->firstName, $this->lastName, $this->email, password_hash($this->password, PASSWORD_DEFAULT), $this->id);

            if ($stmt->execute($params)) {

                return true;
            }
        }
        return false;
    }

    public function delete() {

        $this->id = !empty($this->id) ? filter_var($this->id, FILTER_SANITIZE_NUMBER_INT) : null;

        $query = "DELETE FROM " . $this->table . " WHERE ID=?;";
        $stmt = $this->connection->prepare($query);
        $params = array($this->id);
        if ($stmt->execute($params)) {

            return true;
        }
        return false;
    }

    public function verify($headers) {

        // Client will send JWT via Authorization Bearer JWT header
        // Retrieved with $header["Authorization"]
        if (!empty($headers["Authorization"])) {
            $header = $headers["Authorization"];
            $temp = explode(" ", $header);
            $token = $temp[1];
        }

        // Check if client token exists
        if (!empty($token)) {

            try {

                $decoded = JWT::decode($token, token::$key, array('HS256'));
                $decodedArray = (array) $decoded;
                $this->id = $decodedArray['data'][0];
                $this->firstName = $decodedArray['data'][1];
                $this->lastName = $decodedArray['data'][2];
                $this->email = $decodedArray['data'][3];
                return true;
            } catch (Exception $e) {
                
            }
        }
        return false;
    }

}
