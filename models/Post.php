<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 25/04/2017
 * Time: 19:42
 */
class Post
{
    private $post_id;
    private $title;
    private $desc;
    private $clothes_id;
    private $user_id;

    /**
     * Post constructor.
     * @param $username
     * @param $title
     * @param $desc
     * @param $clothes
     */
    public function __construct($post_id=0, $title="", $desc="", $clothes_id=0, $user_id=0)
    {
        $this->title = $title;
        $this->desc = $desc;
        $this->clothes_id = $clothes_id;
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * @param mixed $post_id
     */
    public function setPostId($post_id)
    {
        $this->post_id = $post_id;
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return int
     */
    public function getClothesId()
    {
        return $this->clothes_id;
    }

    /**
     * @param int $clothes_id
     */
    public function setClothesId($clothes_id)
    {
        $this->clothes_id = $clothes_id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }


}