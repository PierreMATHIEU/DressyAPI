<?php
require_once '../models/Clothe.php';
require_once '../models/Clothes.php';
require_once '../models/Brand.php';
require_once '../models/Material.php';
require_once '../models/Category.php';

require_once '../models/Post.php';

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 25/04/2017
 * Time: 19:49
 */
class DbPostHandler{

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->getDB();
    }

    /**
     * Create post
     * @param Post $post
     */
    public function createPost($post) {

        $stmt = $this->conn->prepare("INSERT INTO post(clothing_id, post_title, post_description, user_id, post_created_at) 
                                              VALUES (:clothing_id, :post_title, :post_description, :user_id, now())
                                              RETURNING post_id");

        $stmt->bindValue(':clothing_id', $post->getClothesId(), PDO::PARAM_INT);
        $stmt->bindValue(':post_title', $post->getTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':post_description', $post->getDesc(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $post->getUserId(), PDO::PARAM_INT);


        if ($stmt->execute()) {

            $post = $stmt->fetch();
            $resPost = $post['post_id'];
            return $resPost;
        } else {
            // Failed to create user
            return false;
        }

    }

    /**
     * get post : top 40
     */
    public function viewTopPost($user_id){
        $postReponse = array();

        $sth = $this->conn->prepare("SELECT post_id, clothing_id, post_title, post_description, user_id FROM post");
        $sth->execute();

        if ($sth) {
            while ($postR = $sth->fetch()) {
                $sth2 = $this->conn->prepare("SELECT user_login
                                              FROM users
                                              WHERE user_id=:userid");
                $sth2->bindValue(':userid', $postR['user_id'] , PDO::PARAM_INT);
                $sth2->execute();
                $sth2Res = $sth2->fetch();

                $dbH = new DbPostHandler();
                $clothes = $dbH->viewSpecifiqueClothes($postR['clothing_id']);

                $newPost = new Post($postR['post_id'], $sth2Res['user_login'],$postR['post_title'], $postR['post_description'], $clothes, $postR['user_id']);
                array_push($postReponse, $newPost);

            }
            return $postReponse;
        } else {
            return false;
        }
    }
    /*------------------------------------------------------------------------------------------*/
    /**
     * View clothes specifique
     * @param int $clothes_id
     */
    public function viewSpecifiqueClothes($clothes_id) {
        $clotheReponce = array();

        $sth = $this->conn->prepare("SELECT clothing_id, clothing_url_image,clothing_vote, user_id
                                              FROM clothing 
                                              WHERE clothing.clothing_id=:clothing_id");
        $sth->bindValue(':clothing_id', $clothes_id, PDO::PARAM_INT);
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
}