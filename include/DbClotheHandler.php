<?php

require_once '../models/Clothe.php';
require_once '../models/Clothes.php';
require_once '../models/Brand.php';
require_once '../models/Material.php';
require_once '../models/Category.php';

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
        $new_brand = array_values($clothe->getClothBrand());
        $new_cat = array_values($clothe->getClothCategory());
        $new_mat = array_values($clothe->getClothMaterial());

      $stmt = $this->conn->prepare("INSERT INTO clothe(cloth_brand_id, cloth_category_id, cloth_material_id, cloth_name, cloth_color, cloth_reference, cloth_urlimage, user_id, cloth_created_at) 
                                              VALUES (:cloth_brand_id, :cloth_category_id, :cloth_material_id, :cloth_name, :cloth_color, :cloth_reference, :cloth_urlimage, :user_id, now())
                                              RETURNING cloth_id");

        $stmt->bindValue(':cloth_brand_id',$new_brand[0], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_category_id', $new_cat[0], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_material_id',$new_mat[0], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_name', $clothe->getClothName(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_color', $clothe->getClothColor(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_reference', $clothe->getClothReference(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_urlimage', $clothe->getClothUrlimage(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $clothe->getUserId(), PDO::PARAM_INT);


        if ($stmt->execute()) {

            $clothe = $stmt->fetch();
            $resCloth = $clothe['cloth_id'];
            return $resCloth;
      } else {
          // Failed to create user
          return false;
      }

    }

    /**
     * Delete clothe
     * @param Clothe $clothe
     */
    public function deleteClothe($clothe){
        $stmt0 = $this->conn->prepare("DELETE FROM clothing_clothe WHERE cloth_id=:cloth_id");
        $stmt0->bindValue(':cloth_id', $clothe->getClothId(), PDO::PARAM_INT);
        $stmt0->execute();

        $stmt = $this->conn->prepare("DELETE FROM clothe WHERE cloth_id=:cloth_id");
        $stmt->bindValue(':cloth_id', $clothe->getClothId(), PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return 0;
        } else {
            return 1;
        }
        $this->conn = null;
        $stmt=null;
    }

    /**
     * View clothe
     * @param int $clothingId
     */
    public function viewAllClothe($user_id) {
        $clotheReponce = array();

        $sth = $this->conn->prepare("SELECT cloth_id, clothe_brand_id, clothe_brand_libelle, user_id, clothe_category_id, clothe_category_libelle, clothe_material_id,clothe_material_libelle, cloth_name, cloth_color, cloth_reference, cloth_urlimage
                                              FROM clothe
                                              JOIN clothe_category ON clothe_category.clothe_category_id=clothe.cloth_category_id 
                                              JOIN clothe_brand ON clothe_brand.clothe_brand_id=clothe.cloth_brand_id
                                              JOIN clothe_material ON clothe_material.clothe_material_id=clothe.cloth_material_id
                                              WHERE user_id=:user_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {

                 $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle']);
                $newCategory = new Category($clothe['clothe_category_id'],$clothe['clothe_category_libelle']);
                $newMaterial = new Material($clothe['clothe_material_id'],$clothe['clothe_material_libelle']);
                $newClothe = new Clothe($clothe['cloth_id'],$clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference'], $clothe['cloth_urlimage'], $newCategory, $newBrand, $newMaterial);
               array_push($clotheReponce, $newClothe);
            }
            $sth->closeCursor();
            $sth = null;
            $this->conn = null;
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


    /*---------------------------------------------CLOTHES---------------------------------------------------*/
    /**
    * View clothes
    * @param int $user_id
    */
    public function viewAllClothes($user_id) {
        $clotheReponce = array();

        $sth = $this->conn->prepare("SELECT clothing_id, clothing_url_image,clothing_vote, user_id
                                              FROM clothing 
                                              WHERE clothing.user_id=:user_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {

            while ($clothes = $sth->fetch()) {
                $listClothe= array();
                $sth2 = $this->conn->prepare("SELECT clothe.cloth_id, clothe_brand_id, clothe_brand_libelle, clothing.user_id, clothe_category_id, clothe_category_libelle, clothe_material_id,clothe_material_libelle, cloth_name, cloth_color, cloth_reference, cloth_urlimage
                                            FROM clothe
                                            JOIN clothe_category ON clothe_category.clothe_category_id=clothe.cloth_category_id 
                                            JOIN clothe_brand ON clothe_brand.clothe_brand_id=clothe.cloth_brand_id
                                            JOIN clothe_material ON clothe_material.clothe_material_id=clothe.cloth_material_id
                                            JOIN clothing_clothe ON clothe.cloth_id=clothing_clothe.cloth_id
                                            JOIN clothing ON clothing.clothing_id=clothing_clothe.clothing_id
                                            WHERE clothing.clothing_id=:clothing_id");
                $sth2->bindValue(':clothing_id', $clothes['clothing_id'] , PDO::PARAM_INT);
                $sth2->execute();

                while ($clothe = $sth2->fetch()) {
                    $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle']);
                    $newCategory = new Category($clothe['clothe_category_id'],$clothe['clothe_category_libelle']);
                    $newMaterial = new Material($clothe['clothe_material_id'],$clothe['clothe_material_libelle']);
                    $newClothe = new Clothe($clothe['cloth_id'],$clothe['cloth_name'], $clothe['cloth_color'], $clothe['cloth_reference'], $clothe['cloth_urlimage'], $newCategory, $newBrand, $newMaterial);
                    array_push($listClothe, $newClothe);
                }
                $sth2->closeCursor();

                $newClothes = new Clothes($clothes['clothing_id'],$clothes['clothing_url_image'],$listClothe, $clothes['clothing_vote'],$clothes['user_id']);
                array_push($clotheReponce, $newClothes);
            }
            $sth->closeCursor();
            $sth2 = null;
            $sth = null;
            $this->conn = null;
            return $clotheReponce;
        } else {
            // Failed
            return false;
        }
    }

    /**
     * Delete clothes
     * @param Clothes $clothes
     */
    public function deleteClothes($clothes){
        $stmt0 = $this->conn->prepare("DELETE FROM clothing_clothe WHERE clothing_id=:clothes_id");
        $stmt0->bindValue(':clothes_id', $clothes->getClothesId(), PDO::PARAM_INT);
        $stmt0->execute();

        $stmt = $this->conn->prepare("DELETE FROM clothing WHERE clothing_id=:clothes_id");
        $stmt->bindValue(':clothes_id', $clothes->getClothesId(), PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return 0;
        } else {
            return 1;
        }
        $this->conn = null;
        $stmt=null;
    }

    /**
     * Create clothe
     * @param Clothe $clothe
     */
    public function createClothes($clothes) {
        $new_brand = array_values($clothes->getClothBrand());
        $new_cat = array_values($clothes->getClothCategory());
        $new_mat = array_values($clothes->getClothMaterial());

        $stmt = $this->conn->prepare("INSERT INTO clothing(user_id, clothing_url_image, clothing_vote) 
                                              VALUES (:user_id, :clothing_url_image, :clothing_vote");

        $stmt->bindValue(':user_id',$clothes->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':clothing_url_image', $clothes->getUrlImage(), PDO::PARAM_STR);
        $stmt->bindValue(':clothing_vote',$clothes->getScore(), PDO::PARAM_STR);


        if ($stmt->execute()) {

            return true;
        } else {
            // Failed to create user
            return false;
        }

    }

    /*----------------------------------------------CLOTHES-PROPERTIES-----------------------------------------*/

    public function viewAllBrand(){
        $clotheReponce = array();
        $sth = $this->conn->prepare("SELECT clothe_brand_id, clothe_brand_libelle
                                              FROM clothe_brand");
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {
                $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle']);
                array_push($clotheReponce, $newBrand);
            }
            $sth->closeCursor();
            $sth = null;
            $this->conn = null;
            return $clotheReponce;
        } else {
            return false;
        }
    }

    public function viewAllCategory(){
        $categoryReponce = array();
        $sth = $this->conn->prepare("SELECT clothe_category_id, clothe_category_libelle
                                              FROM clothe_category");
        $sth->execute();

        if ($sth) {

            while ($category = $sth->fetch()) {
                $newCategory = new Category($category['clothe_category_id'],$category['clothe_category_libelle']);
                array_push($categoryReponce, $newCategory);
            }
            $sth->closeCursor();
            $sth = null;
            $this->conn = null;
            return $categoryReponce;
        } else {
            return false;
        }
    }

    public function viewAllMaterial(){
        $materialReponce = array();
        $sth = $this->conn->prepare("SELECT clothe_material_id, clothe_material_libelle
                                              FROM clothe_material");
        $sth->execute();

        if ($sth) {

            while ($material = $sth->fetch()) {
                $newMaterial = new Material($material['clothe_material_id'],$material['clothe_material_libelle']);
                array_push($materialReponce, $newMaterial);
            }
            $sth->closeCursor();
            $sth = null;
            $this->conn = null;
            return $materialReponce;
        } else {
            return false;
        }
    }


}
