<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 17/04/2017
 * Time: 11:04
 */
class Clothe
{
    public $cloth_id;
    private $cloth_name;
    private $cloth_reference;
    private $cloth_urlimage;
    private $cloth_category= array();
    private $cloth_brand=array();
    private $cloth_material = array();
    private $cloth_color = array();
    private $user_id;

    /**
     * Clothe constructor.
     * @param $cloth_name
     * @param $cloth_color
     * @param $cloth_reference
     * @param $cloth_urlimage
     * @param $cloth_category
     * @param $cloth_brand
     * @param $cloth_material
     */
       public function __construct($cloth_id=0,$cloth_name="", $cloth_reference=null, $cloth_urlimage=null, $cloth_category=array(), $cloth_brand=array(), $cloth_material=array(), $cloth_color=array(), $user_id=0)
    {
        $this->cloth_id = $cloth_id;
        $this->cloth_name = $cloth_name;
        $this->cloth_reference = $cloth_reference;
        $this->cloth_urlimage = $cloth_urlimage;
        $this->cloth_category = $cloth_category;
        $this->cloth_brand = $cloth_brand;
        $this->cloth_material = $cloth_material;
        $this->cloth_color = $cloth_color;
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getClothId()
    {
        return $this->cloth_id;
    }

    /**
     * @param mixed $cloth_Id
     */
    public function setClothId($cloth_id)
    {
        $this->cloth_id = $cloth_id;
    }

    /**
     * @return string
     */
    public function getClothName()
    {
        return $this->cloth_name;
    }

    /**
     * @param string $cloth_name
     */
    public function setClothName($cloth_name)
    {
        $this->cloth_name = $cloth_name;
    }


    /**
     * @return null
     */
    public function getClothReference()
    {
        return $this->cloth_reference;
    }

    /**
     * @param null $cloth_reference
     */
    public function setClothReference($cloth_reference)
    {
        $this->cloth_reference = $cloth_reference;
    }

    /**
     * @return null
     */
    public function getClothUrlimage()
    {
        return $this->cloth_urlimage;
    }

    /**
     * @param null $cloth_urlimage
     */
    public function setClothUrlimage($cloth_urlimage)
    {
        $this->cloth_urlimage = $cloth_urlimage;
    }

    /**
     * @return array
     */
    public function getClothCategory()
    {
        return $this->cloth_category;
    }

    /**
     * @param array $cloth_category
     */
    public function setClothCategory($cloth_category)
    {
        $this->cloth_category = $cloth_category;
    }

    /**
     * @return array
     */
    public function getClothBrand()
    {
        return $this->cloth_brand;
    }

    /**
     * @param array $cloth_brand
     */
    public function setClothBrand($cloth_brand)
    {
        $this->cloth_brand = $cloth_brand;
    }

    /**
     * @return array
     */
    public function getClothMaterial()
    {
        return $this->cloth_material;
    }

    /**
     * @param array $cloth_material
     */
    public function setClothMaterial($cloth_material)
    {
        $this->cloth_material = $cloth_material;
    }

    /**
     * @return array
     */
    public function getClothColor()
    {
        return $this->cloth_color;
    }

    /**
     * @param array $cloth_color
     */
    public function setClothColor($cloth_color)
    {
        $this->cloth_color = $cloth_color;
    }



    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }



}