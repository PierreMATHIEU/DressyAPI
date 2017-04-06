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
        $dbtype="pgsql";
        $dbhost="ec2-54-243-124-240.compute-1.amazonaws.com";
        $dbuser="mfpyjwdlehhhvo";
        $dbpass="008e494e873a65a1b1c70d46ec85074032c08ae7eaa7a53e78ae4c2bb195caa8";
        $dbname="dccpro45ju5lof";

        // $dsn = 'mysql:host=ec2-54-243-124-240.compute-1.amazonaws.com;dbname=dccpro45ju5lof;charset=utf8';
        // $usr = 'mfpyjwdlehhhvo';
        // $pwd = '008e494e873a65a1b1c70d46ec85074032c08ae7eaa7a53e78ae4c2bb195caa8';

        //$pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

        $this->conn = new PDO ("pgsql:host=".$dbhost.";dbname=".$dbname."", "".$dbuser."", "".$dbpass."") or die(print_r($bdd->errorInfo()));
        $this->conn->exec("SET NAMES utf8");
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        //$this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        return $this->conn;
    }

}

?>
