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
 * @param Clothe $clothe
 */
public function createClothe($clothe) {
      $stmt = $this->conn->prepare("INSERT INTO clothe(cloth_brand_id, cloth_category_id, cloth_material_id, cloth_name, cloth_color, cloth_reference, cloth_urlimage, user_id) 
                                              VALUES (:cloth_brand_id, :cloth_category_id, :cloth_material_id, :cloth_name, :cloth_color, :cloth_reference, :cloth_urlimage, :user_id)");

        $stmt->bindValue(':cloth_brand_id', $clothe->getUser_lastName(), PDO::PARAM_INT);
        $stmt->bindValue(':cloth_category_id', $clothe->getUser_fisrtName(), PDO::PARAM_INT);
        $stmt->bindValue(':cloth_material_id', $clothe->getUser_mail(), PDO::PARAM_INT);
        $stmt->bindValue(':cloth_name', $clothe, PDO::PARAM_STR);
        $stmt->bindValue(':cloth_color', $clothe, PDO::PARAM_STR);
        $stmt->bindValue(':cloth_reference', $clothe->getUser_pseudo(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_urlimage', $clothe->getUser_country(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $clothe->getUser_country(), PDO::PARAM_INT);

        //$stmt->close();

      if ($stmt->execute()) {
          // User successfully inserted
          return CLOTHE_CREATED_SUCCESSFULLY;
      } else {
          // Failed to create user
          return CLOTHE_CREATE_FAILED;
      }
    }


    /**
     * View clothe
     * @param int $clothingId
     */
    public function viewAllClothe($user_id) {
        $clotheReponce = array();

        $sth = $this->conn->prepare("SELECT clothe_brand_libelle, user_id, clothe_category_libelle, clothe_material_libelle, cloth_name, cloth_color, cloth_reference, cloth_urlimage
                                              FROM clothe
                                              JOIN clothe_category ON clothe_category.clothe_category_id=clothe.cloth_category_id 
                                              JOIN clothe_brand ON clothe_brand.clothe_brand_id=clothe.cloth_brand_id
                                              JOIN clothe_material ON clothe_material.clothe_material_id=clothe.cloth_material_id
                                              WHERE user_id=:user_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {
                $newClothe = new Clothe($clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference'], $clothe['cloth_urlimage'], $clothe['clothe_category_libelle'], $clothe['clothe_brand_libelle'], $clothe['clothe_material_libelle']);
               array_push($clotheReponce, $newClothe);
            }
            $sth->closeCursor();

            return $clotheReponce;
        } else {
            // Failed
            return false;
        }
    }


    public function viewDetailsClothing($user_id){
        $sth = $this->conn->prepare("SELECT clothing_url_image, clothing_vote
                                              FROM clothing
                                              JOIN clothing_clothe ON clothing_clothe.clothing_id=clothing.clothing_id
                                              WHERE user_id=:user_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {
            $clothe = $sth->fetch();
            $clotheReponce = new Clothes($clothe['clothing_url_image'], $clothe['clothing_vote']);
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
