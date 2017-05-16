<?php

require_once '../models/Clothe.php';
require_once '../models/Clothes.php';
require_once '../models/Brand.php';
require_once '../models/Material.php';
require_once '../models/Category.php';
require_once '../models/Color.php';

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
        $new_brand = $clothe->getClothBrand();
        $new_cat = $clothe->getClothCategory();
        $new_mat = $clothe->getClothMaterial();
        $new_col = $clothe->getClothColor();

      $stmt = $this->conn->prepare("INSERT INTO clothe(cloth_brand_id, cloth_category_id, cloth_material_id, cloth_name, cloth_reference, cloth_urlimage, user_id, cloth_created_at,cloth_color_id) 
                                              VALUES (:cloth_brand_id, :cloth_category_id, :cloth_material_id, :cloth_name, :cloth_reference, :cloth_urlimage, :user_id, now(), :cloth_color)
                                              RETURNING cloth_id");

        $stmt->bindValue(':cloth_brand_id',$new_brand['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_category_id', $new_cat['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_material_id',$new_mat['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_name', $clothe->getClothName(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_color', $new_col['id'], PDO::PARAM_STR);
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
     * Update clothe
     * @param Clothe $clothe
     */
    public function updateClothe($clothe) {
        $new_brand = $clothe->getClothBrand();
        $new_cat = $clothe->getClothCategory();
        $new_mat = $clothe->getClothMaterial();
        $new_col = $clothe->getClothColor();

        $stmt = $this->conn->prepare("UPDATE clothe SET cloth_brand_id=:cloth_brand_id, cloth_category_id=:cloth_category_id, cloth_material_id=:cloth_material_id, cloth_name=:cloth_name, cloth_reference=:cloth_reference, cloth_urlimage=:cloth_urlimage, user_id=:user_id, cloth_created_at=now(), cloth_color_id=:cloth_color
                                                WHERE cloth_id = :cloth_id
                                              RETURNING cloth_id");

        $stmt->bindValue(':cloth_id',$clothe->getClothId(), PDO::PARAM_INT);
        $stmt->bindValue(':cloth_brand_id',$new_brand['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_category_id', $new_cat['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_material_id',$new_mat['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_name', $clothe->getClothName(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_color', $new_col['id'], PDO::PARAM_INT);
        $stmt->bindValue(':cloth_reference', $clothe->getClothReference(), PDO::PARAM_STR);
        $stmt->bindValue(':cloth_urlimage', $clothe->getClothUrlimage(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $clothe->getUserId(), PDO::PARAM_INT);


        if ($stmt->execute()) {

            return $clothe->getClothId();
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

        $sth = $this->conn->prepare("SELECT cloth_id, clothe_brand_id, clothe_brand_libelle,clothe_brand_id_fann,clothe_color_id, clothe_color_libelle,clothe_color_id_fann, user_id, clothe_category_id, clothe_category_libelle,clothe_category_id_fann, clothe_material_id,clothe_material_libelle,clothe_material_id_fann, cloth_name, cloth_reference, cloth_urlimage
                                              FROM clothe
                                              JOIN clothe_category ON clothe_category.clothe_category_id=clothe.cloth_category_id 
                                              JOIN clothe_brand ON clothe_brand.clothe_brand_id=clothe.cloth_brand_id
                                              JOIN clothe_material ON clothe_material.clothe_material_id=clothe.cloth_material_id
                                              JOIN clothe_color ON clothe_color.clothe_color_id=clothe.cloth_color_id
                                              WHERE user_id=:user_id");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {
                $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle'],$clothe['clothe_brand_id_fann']);
                $newCategory = new Category($clothe['clothe_category_id'],$clothe['clothe_category_libelle'], $clothe['clothe_category_id_fann']);
                $newMaterial = new Material($clothe['clothe_material_id'],$clothe['clothe_material_libelle'], $clothe['clothe_material_id_fann']);
                $newColor= new Color($clothe['clothe_color_id'],$clothe['clothe_color_libelle'], $clothe['clothe_color_id_fann']);
                $newClothe = new Clothe($clothe['cloth_id'],$clothe['cloth_name'], $clothe['cloth_reference'], $clothe['cloth_urlimage'], $newCategory, $newBrand, $newMaterial, $newColor);
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
                $sth2 = $this->conn->prepare("SELECT clothe.cloth_id, clothe_brand_id, clothe_brand_libelle,clothe_brand_id_fann,clothe_color_id, clothe_color_libelle,clothe_color_id_fann, clothing.user_id, clothe_category_id, clothe_category_libelle,clothe_category_id_fann, clothe_material_id,clothe_material_libelle,clothe_material_id_fann, cloth_name, cloth_reference, cloth_urlimage
                                            FROM clothe
                                            JOIN clothe_category ON clothe_category.clothe_category_id=clothe.cloth_category_id 
                                            JOIN clothe_brand ON clothe_brand.clothe_brand_id=clothe.cloth_brand_id
                                            JOIN clothe_material ON clothe_material.clothe_material_id=clothe.cloth_material_id
                                            JOIN clothe_color ON clothe_color.clothe_color_id=clothe.cloth_color_id
                                            JOIN clothing_clothe ON clothe.cloth_id=clothing_clothe.cloth_id
                                            JOIN clothing ON clothing.clothing_id=clothing_clothe.clothing_id
                                            WHERE clothing.clothing_id=:clothing_id");
                $sth2->bindValue(':clothing_id', $clothes['clothing_id'] , PDO::PARAM_INT);
                $sth2->execute();

                while ($clothe = $sth2->fetch()) {
                    $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle'],$clothe['clothe_brand_id_fann']);
                    $newCategory = new Category($clothe['clothe_category_id'],$clothe['clothe_category_libelle'],$clothe['clothe_category_id_fann']);
                    $newMaterial = new Material($clothe['clothe_material_id'],$clothe['clothe_material_libelle'],$clothe['clothe_material_id_fann']);
                    $newColor= new Color($clothe['clothe_color_id'],$clothe['clothe_color_libelle'],$clothe['clothe_color_id_fann']);
                    $newClothe = new Clothe($clothe['cloth_id'],$clothe['cloth_name'], $clothe['cloth_reference'], $clothe['cloth_urlimage'], $newCategory, $newBrand, $newMaterial,$newColor);
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
     * @param Clothe $clothe, Array de clothid : $clotheArray
     */
    public function createClothes($clothes, $clotheArray) {
        $stmt = $this->conn->prepare("INSERT INTO clothing(user_id, clothing_url_image, clothing_vote) 
                                              VALUES (:user_id, :clothing_url_image, :clothing_vote)
                                              RETURNING clothing_id
                                              ");

        $stmt->bindValue(':user_id',$clothes->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':clothing_url_image', $clothes->getUrlImage(), PDO::PARAM_STR);
        $stmt->bindValue(':clothing_vote',$clothes->getScore(), PDO::PARAM_STR);

        if ($stmt->execute()) {

            $clothes = $stmt->fetch();
            $resClothes_id = $clothes['clothing_id'];

            foreach ($clotheArray as $clotheValue){
                $stmt2 = $this->conn->prepare("INSERT INTO clothing_clothe(clothing_id, cloth_id) 
                                              VALUES (:clothing_id, :cloth_id)");
                $stmt2->bindValue(':clothing_id',$resClothes_id, PDO::PARAM_INT);
                $stmt2->bindValue(':cloth_id', $clotheValue, PDO::PARAM_INT);
                $stmt2->execute();

            }

            return $resClothes_id;
        } else {
            // Failed to create user
            return false;
        }

    }

    /**
     * Create clothe
     * @param Clothe $clothe, Array de clothid : $clotheArray
     */
    public function updateClothes($clothes, $clotheArray) {
        $stmt = $this->conn->prepare("UPDATE clothing SET user_id=:user_id, clothing_url_image=:clothing_url_image, clothing_vote=:clothing_vote
                                                WHERE clothing_id = :clothing_id");

        $stmt->bindValue(':clothing_id',$clothes->getClothesId(), PDO::PARAM_INT);
        $stmt->bindValue(':user_id',$clothes->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':clothing_url_image', $clothes->getUrlImage(), PDO::PARAM_STR);
        $stmt->bindValue(':clothing_vote',$clothes->getScore(), PDO::PARAM_INT);



        if ($stmt->execute()) {
            $resClothes_id = $clothes->getClothesId();

            foreach ($clotheArray as $clotheValue){
                $stmt2 = $this->conn->prepare("DELETE FROM clothing_clothe 
                                                        WHERE clothing_id=:clothing_id");

                $stmt2->bindValue(':clothing_id',$resClothes_id, PDO::PARAM_INT);
                $stmt2->execute();
            }

            foreach ($clotheArray as $clotheValue){
                $stmt3 = $this->conn->prepare("INSERT INTO clothing_clothe(clothing_id, cloth_id) 
                                              VALUES (:clothing_id, :cloth_id)");

                $stmt3->bindValue(':clothing_id',$resClothes_id, PDO::PARAM_INT);
                $stmt3->bindValue(':cloth_id', $clotheValue, PDO::PARAM_INT);
                $stmt3->execute();
            }

            return $resClothes_id;
        } else {
            // Failed to create user
            return false;
        }

    }

    /*----------------------------------------------CLOTHES-PROPERTIES-----------------------------------------*/

    public function viewAllBrand(){
        $clotheReponce = array();
        $sth = $this->conn->prepare("SELECT clothe_brand_id, clothe_brand_libelle, clothe_brand_id_fann
                                              FROM clothe_brand");
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {
                $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle'],$clothe['clothe_brand_id_fann']);
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
        $sth = $this->conn->prepare("SELECT clothe_category_id, clothe_category_libelle,clothe_category_id_fann
                                              FROM clothe_category");
        $sth->execute();

        if ($sth) {

            while ($category = $sth->fetch()) {
                $newCategory = new Category($category['clothe_category_id'],$category['clothe_category_libelle'],$category['clothe_category_id_fann']);
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
        $sth = $this->conn->prepare("SELECT clothe_material_id, clothe_material_libelle, clothe_material_id_fann
                                              FROM clothe_material");
        $sth->execute();

        if ($sth) {

            while ($material = $sth->fetch()) {
                $newMaterial = new Material($material['clothe_material_id'],$material['clothe_material_libelle'],$material['clothe_material_id_fann']);
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

    public function viewAllColor(){
        $colorReponce = array();
        $sth = $this->conn->prepare("SELECT clothe_color_id, clothe_color_libelle, clothe_color_id_fann
                                              FROM clothe_color");
        $sth->execute();

        if ($sth) {

            while ($color = $sth->fetch()) {
                $newColor = new Color($color['clothe_color_id'],$color['clothe_color_libelle'], $color['clothe_color_id_fann']);
                array_push($colorReponce, $newColor);
            }
            $sth->closeCursor();
            $sth = null;
            $this->conn = null;
            return $colorReponce;
        } else {
            return false;
        }
    }

    /*----------------------------------------------CLOTHES-SIMILARITY-----------------------------------------*/

    public function getSimilarity($user_id,$newSimilarirtTab){
        $clotheReponce = array();
        $sth = $this->conn->prepare("SELECT cloth_id, clothe_brand_id, clothe_brand_libelle,clothe_brand_id_fann,clothe_color_id, clothe_color_libelle,clothe_color_id_fann, user_id, clothe_category_id, clothe_category_libelle,clothe_category_id_fann, clothe_material_id,clothe_material_libelle,clothe_material_id_fann, cloth_name, cloth_reference, cloth_urlimage                                              
                                              FROM clothe
                                              JOIN clothe_category ON clothe_category.clothe_category_id=clothe.cloth_category_id 
                                              JOIN clothe_brand ON clothe_brand.clothe_brand_id=clothe.cloth_brand_id
                                              JOIN clothe_material ON clothe_material.clothe_material_id=clothe.cloth_material_id
                                              JOIN clothe_color ON clothe_color.clothe_color_id=clothe.cloth_color_id
                                              WHERE user_id=:user_id
                                              AND clothe_category_id_fann=:clothe_category_id_fann AND clothe_color_id_fann=:clothe_color_id_fann 
                                              AND clothe_material_id_fann=:clothe_material_id_fann");
        $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sth->bindValue(':clothe_category_id_fann', $newSimilarirtTab[0]);
        $sth->bindValue(':clothe_color_id_fann', $newSimilarirtTab[2]);
        $sth->bindValue(':clothe_material_id_fann', $newSimilarirtTab[1]);
        $sth->execute();

        if ($sth) {

            while ($clothe = $sth->fetch()) {
                $newBrand = new Brand($clothe['clothe_brand_id'],$clothe['clothe_brand_libelle'],$clothe['clothe_brand_id_fann']);
                $newCategory = new Category($clothe['clothe_category_id'],$clothe['clothe_category_libelle'], $clothe['clothe_category_id_fann']);
                $newMaterial = new Material($clothe['clothe_material_id'],$clothe['clothe_material_libelle'], $clothe['clothe_material_id_fann']);
                $newColor= new Color($clothe['clothe_color_id'],$clothe['clothe_color_libelle'], $clothe['clothe_color_id_fann']);
                $newClothe = new Clothe($clothe['cloth_id'],$clothe['cloth_name'], $clothe['cloth_reference'], $clothe['cloth_urlimage'], $newCategory, $newBrand, $newMaterial, $newColor);
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
}
