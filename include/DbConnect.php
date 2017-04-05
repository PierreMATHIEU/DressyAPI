<?php

/**
 * Connection avec la base de données
 */
class DbConnect {

    private $conn;

    function __construct() {
    }

    /**
     * Etablit la connection avec la BDD
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';

        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // returing connection resource
        return $this->conn;
    }

}

?>
