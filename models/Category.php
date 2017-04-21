<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 20/04/2017
 * Time: 21:58
 */
class Category
{
    private $category_id=0;
    private $category_libelle="";

    /**
     * Category constructor.
     * @param $category_id
     * @param $category_libelle
     */
    public function __construct($category_id=0, $category_libelle="")
    {
        $this->category_id = $category_id;
        $this->category_libelle = $category_libelle;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * @return string
     */
    public function getCategoryLibelle()
    {
        return $this->category_libelle;
    }

    /**
     * @param string $category_libelle
     */
    public function setCategoryLibelle($category_libelle)
    {
        $this->category_libelle = $category_libelle;
    }


}