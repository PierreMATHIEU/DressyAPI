<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 17/04/2017
 * Time: 11:22
 */
class Clothes
{
    private $clothes_id;
    private $urlImage;
    private $listClothe = array();
    private $score = 0;

    /**
     * Clothes constructor.
     * @param $urlImage
     * @param array $listClothe
     * @param $score
     */
    public function __construct($clothes_id=0,$urlImage="",$listClothe=array(), $score=0)
    {
        $this->clothes_id = $clothes_id;
        $this->urlImage = $urlImage;
        $this->listClothe = $listClothe;
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getClothesId()
    {
        return $this->clothes_id;
    }

    /**
     * @param mixed $clothes_id
     */
    public function setClothesId($clothes_id)
    {
        $this->clothes_id = $clothes_id;
    }


    /**
     * @return mixed
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }

    /**
     * @param mixed $urlImage
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;
    }

    /**
     * @return array
     */
    public function getListClothe()
    {
        return $this->listClothe;
    }

    /**
     * @param array $listClothe
     */
    public function setListClothe($listClothe)
    {
        $this->listClothe = $listClothe;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }


}