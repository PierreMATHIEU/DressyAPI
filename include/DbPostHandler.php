<?php

require_once '../models/Clothe.php';
require_once '../models/Clothes.php';
require_once '../models/Brand.php';
require_once '../models/Material.php';
require_once '../models/Category.php';

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 25/04/2017
 * Time: 19:49
 */
class DbPostHandler
{
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