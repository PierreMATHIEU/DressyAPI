<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 17/04/2017
 * Time: 11:22
 */
class Clothes
{
    private $urlImage;
    private $listClothe = array();
    private $score = 0;

    /**
     * Clothes constructor.
     * @param $urlImage
     * @param array $listClothe
     * @param $score
     */
    public function __construct($urlImage, $score)
    {
        $this->urlImage = $urlImage;
        $this->score = $score;
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