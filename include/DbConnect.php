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
     public function getDB()
     {
/*       $dbtype="pgsql";
       $dbhost="ec2-54-243-124-240.compute-1.amazonaws.com";
       $dbuser="mfpyjwdlehhhvo";
       $dbpass="008e494e873a65a1b1c70d46ec85074032c08ae7eaa7a53e78ae4c2bb195caa8";
       $dbname="dccpro45ju5lof";

       $dbConnection = new PDO ("pgsql:host=".$dbhost.";dbname=".$dbname."", "".$dbuser."", "".$dbpass."") or die(print_r($bdd->errorInfo()));
       $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
*/
         $dbtype="pgsql";
         $dbhost="51.254.101.16";
         $dbuser="postgres";
         $dbpass="RNy4RP3e";
         $dbname="postgres";

         $dbConnection = new PDO ("pgsql:host=".$dbhost.";dbname=".$dbname."", "".$dbuser."", "".$dbpass."") or die(print_r($bdd->errorInfo()));
         $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         return $dbConnection;
     }

}

?>
