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
    public function createUser($user) {
        require_once 'PassHash.php';
        $response = array();
        //$name = $user.getName();

        //Check si l'adresse email éxiste
        $sth = $this->conn->prepare("SELECT * FROM users WHERE user_mail = :email");
        $sth->bindValue(':email', $user->getUser_mail(), PDO::PARAM_STR);
        $sth->execute();


        if ($sth->rowCount() > 0){
            return 3;
        } else {
          // Generating password hash
          $password_hash = PassHash::hash($user->getUser_password());

          // Generating API key
          $api_key = $this->generateApiKey();

          $stmt = $this->conn->prepare("INSERT INTO users(user_id, user_type_id, user_last_name, user_first_name, user_mail, user_password, user_api_key, user_login, user_country) values(nextval('index_sequence'), 1,:name,:firstname,:mail,:password,:apikey,:login,:country)");

          $stmt->bindValue(':name', $user->getUser_lastName(), PDO::PARAM_STR);
           $stmt->bindValue(':firstname', $user->getUser_fisrtName(), PDO::PARAM_STR);
           $stmt->bindValue(':mail', $user->getUser_mail(), PDO::PARAM_STR);
           $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
           $stmt->bindValue(':apikey', $api_key, PDO::PARAM_STR);
           $stmt->bindValue(':login', $user->getUser_pseudo(), PDO::PARAM_STR);
           $stmt->bindValue(':country', $user->getUser_country(), PDO::PARAM_STR);
           $stmt->execute();

          // Check for successful insertion
          if ($stmt) {
              // User successfully inserted
              return $api_key;
          } else {
              // Failed to create user
              return 2;
          }
          $stmt->close();
        }
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($user) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT user_password FROM users WHERE user_login = :login");
        $stmt->bindValue(':login', $user->getUser_pseudo(), PDO::PARAM_STR);
        $stmt->execute();

        $res = $stmt->fetch();
        $password_hash = $res['user_password'];

        if ($stmt->rowCount()> 0) {
            // Found user with the email
            // Now verify the password

            if (PassHash::check_password($password_hash, $user->getUser_password())) {
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
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($user) {
        $stmt = $this->conn->prepare("SELECT user_api_key FROM users WHERE user_login = :login");
        $stmt->bindValue(':login', $user->getUser_pseudo(), PDO::PARAM_STR);

        if ($stmt->execute()) {
            $res = $stmt->fetch();
            $api_key = $res['user_api_key'];
            //$user = array();
            //$user["user_api_key"] = $api_key;
            return $api_key;
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
