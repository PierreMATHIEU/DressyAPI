<?php

require_once '../models/Post.php';

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
    public function viewTopPost($user_id){
        $postReponse = array();

        $sth = $this->conn->prepare("SELECT post_id, clothing_id, post_title, post_description, user_id FROM post");
        $sth->execute();
        $sth2 = $this->conn->prepare("SELECT login
                                              FROM users
                                              WHERE user_id=:userid");
        $sth2->bindValue(':userid', $user_id , PDO::PARAM_INT);
        $sth2->execute();

        $sth2Res = $sth2->fetch();

        if ($sth) {
            var_dump("og");
            while ($post = $sth->fetch()) {
                var_dump($post);
                $newPost = new Post($post['post_id'], $sth2Res,$post['post_title'], $post['post_description'], $post['clothing_id'], $post['user_id']);
                array_push($postReponse, $newPost);
            }

            return $postReponse;
        } else {
            return false;
        }
    }

}