<?php

include_once './config/core.php';
include_once './config/database.php';
include_once './models/userModel.php';
include_once './models/tokenModel.php';
include_once './libs/php-jwt-master/src/BeforeValidException.php';
include_once './libs/php-jwt-master/src/ExpiredException.php';
include_once './libs/php-jwt-master/src/SignatureInvalidException.php';
include_once './libs/php-jwt-master/src/JWT.php';

        /**
         * INFO FOR USER API CONNECTION
         * 
         *  New user
         *   - firstName string
         *   - lastName string
         *   - email string
         *   - password string
         *   - confirmPassword string
         *   - create boolean
         * 
         *  Read user
         *   - loginEmail string
         *   - loginPassword string
         *   - read boolean
         *   - returns - JWT
         *  
         * Update user
         *   - JWT in header
         *   - firstName string
         *   - lastName string
         *   - email string
         *   - password string
         *   - confirmPassword string
         *   - returns - JWT
         * 
         * Delete user
         *   - JWT in header
         *   - password string
         */

switch ($_SERVER['REQUEST_METHOD']) {

    case 'POST':

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->create)) {
            // Create

            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);

            // set product property values
            // TODO make function to return 400 if parameters empty, passwords not long enough
            $user->firstName = $data->firstName;
            $user->lastName = $data->lastName;
            $user->email = $data->email;
            $user->password = $data->password;
            $user->confirmPassword = $data->confirmPassword;

            if ($user->create()) {

                http_response_code(200);

                echo json_encode(array("message" => "User was created."));
                break;
            }
        } elseif (!empty($data->read)) {
            // Read

            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            $user->email = $data->loginEmail;
            $user->password = $data->loginPassword;

            $email_exists = $user->read();

            if ($email_exists && password_verify($data->loginPassword, $user->password)) {

                http_response_code(200);

                // generate jwt
                $jwt = new token($user);
                $jwt->create();

                echo json_encode(
                        array(
                            "message" => "Successful login.",
                            "jwt" => $jwt->jwt
                        )
                );
                break;
            }
        }

        http_response_code(400);
        echo json_encode(array("message" => "Bad request."));
        break;

    case 'PUT':
        // Update

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: PUT");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $data = json_decode(file_get_contents("php://input"));

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);

        // Get client token
        $headers = apache_request_headers();
        $header = !empty($headers["Authorization"]) ? $headers["Authorization"] : "";
        $temp = explode(" ", $header);
        $token = $temp[1];

        // Check if client token exists
        if ($token) {

            try {

                $decoded = JWT::decode($token, token::$key, array('HS256'));

                $user->firstName = $data->firstName;
                $user->lastName = $data->lastName;
                $user->email = $data->email;
                $user->password = $data->password;
                $user->confirmPassword = $data->confirmPassword;
                $decodedArray = (array) $decoded;
                $user->id = $decodedArray['data'][0];

                if ($user->update()) {

                    // generate jwt
                    $jwt = new token($user);
                    $jwt->create();
                    http_response_code(200);
                    echo json_encode(
                            array(
                                "message" => "Update successful.",
                                "jwt" => $jwt->jwt
                            )
                    );
                } else {

                    http_response_code(401);
                    echo json_encode(array("message" => "Unable to update user."));
                }

                http_response_code(200);
                return true;
            } catch (Exception $ex) {

                // set response code
                http_response_code(401);

                // tell the user access denied  & show error message
                echo json_encode(array(
                    "message" => "Access denied.",
                    "error" => $ex->getMessage()
                ));
            }
        } else {

            http_response_code(401);
            echo json_encode(array("message" => "Access denied."));
        }

        http_response_code(400);
        echo json_encode(array("message" => "Bad Request"));
        break;

    case 'DELETE':

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $headers = apache_request_headers();
        $data = json_decode(file_get_contents("php://input"));

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);

        // Verify JWT
        if ($user->verify($headers)) {

            // Verify email and get user password to compare
            $email_exists = $user->read();

            if ($email_exists && password_verify($data->password, $user->password)) {
                echo "email/password valid\n";
                if ($user->delete()) {

                    http_response_code(200);
                    echo json_encode(array("message" => "User deleted."));
                    break;
                }
            }
        }

        http_response_code(400);
        echo json_encode(array("message" => "Bad Request"));

        break;

    default:
        http_response_code(400);
        echo json_encode(array("message" => "Bad Request"));
        break;
}
