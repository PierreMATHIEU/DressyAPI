<?php

require_once '../models/Clothe.php';

class DbClotheHandler {

  private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->getDB();
    }

  /**
 * Create clothe
 * @param String $clotheName
 * @param String $clotheColor
 * @param String $clotheReference
 */
public function createClothe($clotheName, $clotheColor, $clotheReference) {
      $stmt = $this->conn->prepare("INSERT INTO clothe(clothe_brand_id, clothe_category_id, clothe_material_id, clothe_name, clothe_color, clothe_reference) VALUES (1, 1, 1, ?, ?, ?)");
      $stmt->bind_param("sss", $clotheName, $clotheColor, $clotheReference);

      $result = $stmt->execute();
      $stmt->close();

      if ($result) {
          // User successfully inserted
          return CLOTHE_CREATED_SUCCESSFULLY;
      } else {
          // Failed to create user
          return CLOTHE_CREATE_FAILED;
      }
    }


    /**
     * View clothing
     * @param int $clothingId
     */
    public function viewClothing($user_id,$clothing_id) {
        $clotheReponce = array();
        //$sth = $this->conn->prepare("SELECT * FROM clothe");
        $sth = $this->conn->prepare("SELECT * 
                                              FROM clothe
                                              JOIN clothing_clothe ON clothe.cloth_id=clothing_clothe.cloth_id
                                              JOIN clothing ON clothing_clothe.clothing_id=clothing.clothing_id
                                              WHERE user_id=:user_id
                                              AND clothing.clothing_id=:clothing_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->bindValue(':clothing_id', $clothing_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {
                $newClothe = new Clothe($clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference'], $clothe['cloth_urlImage']);
                array_push($clotheReponce, $newClothe);
            }
            $sth->closeCursor();

            return $clotheReponce;
        } else {
            // Failed
            return false;
        }
    }

/**
* View clothe
* @param int $clothingId
*/
public function viewClothe() {
    $clotheReponce = array();
    $sth = $this->conn->prepare("SELECT * FROM clothe");

    $sth->execute();

    if ($sth) {

        while ($clothe = $sth->fetch()) {
            //var_dump("popodipopo");
            $newClothe = new Clothe($clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference']);
            //var_dump($newClothe);
            array_push($clotheReponce, $newClothe);
        }
        $sth->closeCursor();

        return $clotheReponce;
    } else {
        // Failed
        return false;
    }
  }
}
