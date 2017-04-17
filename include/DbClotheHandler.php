<?php

require_once '../models/Clothe.php';
require_once '../models/Clothes.php';

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
        $sth = $this->conn->prepare("SELECT cloth_brand_id, cloth_category_id, cloth_material_id, cloth_name, cloth_color, cloth_reference, cloth_urlimage
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
                $newClothe = new Clothe($clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference'], $clothe['cloth_urlimage']);
               array_push($clotheReponce, $newClothe);
            }
            $sth->closeCursor();

            return $clotheReponce;
        } else {
            // Failed
            return false;
        }
    }
    public function viewDetailsClothing($user_id,$clothing_id){
        $clotheReponce = array();
        //$sth = $this->conn->prepare("SELECT * FROM clothe");
        $sth = $this->conn->prepare("SELECT clothing_url_image, clothing_vote
                                              FROM clothing
                                              JOIN clothing_clothe ON clothing_clothe.clothing_id=clothing.clothing_id
                                              WHERE user_id=:user_id
                                              AND clothing.clothing_id=:clothing_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->bindValue(':clothing_id', $clothing_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {
            while ($clothe = $sth->fetch()) {
                $newClothe = new Clothes($clothe['clothing_url_image'], $clothe['clothing_vote']);
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
            $newClothe = new Clothe($clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference']);
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
