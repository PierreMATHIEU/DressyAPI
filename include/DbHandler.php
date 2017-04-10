<?php

/**
*Gère les évènement avec la base de données -> CRUD
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->getDB();
    }


    /* ------------- `users` table method ------------------ */
    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createUser($name, $email, $password) {
        //require_once 'PassHash.php';
        //$response = array();

        // First check if user already existed in db
        // if (!$this->isUserExists($email)) {
            // Generating password hash
            //$password_hash = PassHash::hash($password);

            // Generating API key
            //$api_key = $this->generateApiKey();

            // insert query
            //  $stmt = $this->conn->prepare("INSERT INTO users (user_last_name, user_first_name, user_mail, user_password, user_api_key, user_login, user_country) values(?, 'popo', ?, ?, ?, 'aa','aa')");
            //  $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);
            //  $result = $stmt->execute();

             $stmt = $this->conn->prepare("INSERT INTO users (user_last_name, user_first_name, user_mail, user_password, user_api_key, user_login, user_country) values(':name', ':firstname', ':mail', ':password', ':apikey', ':login', ':country')");

              // $stmt->bindParam(':name', '$name', PDO::PARAM_STR);
              // $stmt->bindParam(':firstname', 'aa', PDO::PARAM_STR);
              // $stmt->bindParam(':mail', '$email', PDO::PARAM_STR);
              // $stmt->bindParam(':password', '$password_hash', PDO::PARAM_STR);
              // $stmt->bindParam(':apikey', '$api_key', PDO::PARAM_STR);
              // $stmt->bindParam(':login', 'login', PDO::PARAM_STR);
              // $stmt->bindParam(':country', 'country', PDO::PARAM_STR);
              $stmt->execute();


              $stmt->close();

            // Check for successful insertion
            if ($stmt) {
                // User successfully inserted
                return 1;
            } else {
                // Failed to create user
                return 2;
            }
        // } else {
        //     // User with same email already existed in the db
        //     return 3;
        // }

        return $response;
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT user_password FROM user WHERE user_mail = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT user_id from users WHERE user_mail = :email");

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT user_last_name, user_mail, user_api_key FROM user WHERE user_mail = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($name, $email, $api_key);
            $stmt->fetch();
            $user = array();
            $user["user_last_name"] = $name;
            $user["user_mail"] = $email;
            $user["user_api_key"] = $api_key;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT user_api_key FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            // $api_key = $stmt->get_result()->fetch_assoc();
            // TODO
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT user_id FROM user WHERE user_api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT user_id from user WHERE user_api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

}
