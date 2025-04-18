<?php
class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->username) && !empty($data->email) && !empty($data->password)) {
            $this->user->username = $data->username;
            $this->user->email = $data->email;

            // Hash the password before storing it
            $this->user->password = password_hash($data->password, PASSWORD_BCRYPT);

            if ($this->user->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "User was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create user. Data is incomplete."));
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->email) && !empty($data->password)) {
            $this->user->email = $data->email;
            $email_exists = $this->user->emailExists();
            
            if ($email_exists && password_verify($data->password, $this->user->password)) {
                $token = array(
                   "iat" => time(),
                   "exp" => time() + (60 * 60), // 1 hour
                   "data" => array(
                       "id" => $this->user->id,
                       "username" => $this->user->username,
                       "email" => $this->user->email
                   )
                );
                
                http_response_code(200);
                echo json_encode(
                    array(
                        "message" => "Successful login.",
                        "token" => $token
                    )
                );
            } else {
                http_response_code(401);
                echo json_encode(array("message" => "Login failed."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to login. Data is incomplete."));
        }
    }
   
    public function logout() {
        // End the session
        session_start();
        session_destroy();

        http_response_code(200);
        echo json_encode(array("message" => "Logged out successfully."));
    }
}
?>
