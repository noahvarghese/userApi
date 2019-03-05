<?php

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of token
 *
 * @author user
 */
class token {

    //put your code here
    public static $key = "C12170C73199F150EAD63C8181F0F2A37D0F958E1892C765DF840BDA18A5331654F6855BEA09D5DFBE29B906C1425E4C7CDA7540875F34E457629ECFADC81CD9";
    private static $issuer = "localhost";
    private static $audience = "localhost";
    public $issuedAt;
    public $notBefore;
    public $expiration;
    public $data;
    public $jti;
    public $jwt;

    public function __construct($user) {
        $this->issuedAt = time();
        $this->notBefore = $this->issuedAt + 60;
        $this->expiration = $this->notBefore + (60 * 60);
        $this->jti = $user->id;
        $this->data = array($user->id, $user->firstName, $user->lastName, $user->email);
    }

    public function create() {
        $token = array(
            "iss" => token::$issuer,
            "aud" => token::$audience,
            "iat" => $this->issuedAt,
            "nbf" => $this->notBefore,
            "exp" => $this->expiration,
            "jti" => $this->jti,
            "data" => $this->data
        );
        $this->jwt = JWT::encode($token, token::$key);
    }

    public function read() {
        try {
            $data = JWT::decode($this->jwt, token::$key, array('HS256'));
            echo json_encode(array("data" => $data));
        } catch (Exception $ex) {
            return false;
        }
    }
}
