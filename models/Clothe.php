<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 17/04/2017
 * Time: 11:04
 */
class Clothe
{
    private $cloth_Id;
    private $cloth_name;
    private $cloth_color;
    private $cloth_reference;
    private $cloth_urlimage;
    private $cloth_category;
    private $cloth_brand;
    private $cloth_material;
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
       public function __construct($cloth_id=0,$cloth_name="", $cloth_color="", $cloth_reference=null, $cloth_urlimage=null, $cloth_category=null, $cloth_brand=null, $cloth_material=null, $user_id=0)
    {
        $this->cloth_name = $cloth_name;
        $this->cloth_id = $cloth_id;
        $this->cloth_color = $cloth_color;
        $this->cloth_reference = $cloth_reference;
        $this->cloth_urlimage = $cloth_urlimage;
        $this->cloth_category = $cloth_category;
        $this->cloth_brand = $cloth_brand;
        $this->cloth_material = $cloth_material;
    }

    /**
     * @return mixed
     */
    public function getClothId()
    {
        return $this->cloth_Id;
    }

    /**
     * @param mixed $cloth_Id
     */
    public function setClothId($cloth_Id)
    {
        $this->cloth_Id = $cloth_Id;
    }


    /**
     * @return mixed
     */
    public function getClothName()
    {
        return $this->cloth_name;
    }

    /**
     * @param mixed $cloth_name
     */
    public function setClothName($cloth_name)
    {
        $this->cloth_name = $cloth_name;
    }

    /**
     * @return mixed
     */
    public function getClothColor()
    {
        return $this->cloth_color;
    }

    /**
     * @param mixed $cloth_color
     */
    public function setClothColor($cloth_color)
    {
        $this->cloth_color = $cloth_color;
    }

    /**
     * @return mixed
     */
    public function getClothReference()
    {
        return $this->cloth_reference;
    }

    /**
     * @param mixed $cloth_reference
     */
    public function setClothReference($cloth_reference)
    {
        $this->cloth_reference = $cloth_reference;
    }

    /**
     * @return mixed
     */
    public function getClothUrlImage()
    {
        return $this->cloth_urlimage;
    }

    /**
     * @param mixed $cloth_urlImage
     */
    public function setClothUrlImage($cloth_urlimage)
    {
        $this->cloth_urlimage = $cloth_urlimage;
    }

    /**
     * @return mixed
     */
    public function getClothCategory()
    {
        return $this->cloth_category;
    }

    /**
     * @param mixed $cloth_category
     */
    public function setClothCategory($cloth_category)
    {
        $this->cloth_category = $cloth_category;
    }

    /**
     * @return mixed
     */
    public function getClothBrand()
    {
        return $this->cloth_brand;
    }

    /**
     * @param mixed $cloth_brand
     */
    public function setClothBrand($cloth_brand)
    {
        $this->cloth_brand = $cloth_brand;
    }

    /**
     * @return mixed
     */
    public function getClothMaterial()
    {
        return $this->cloth_material;
    }

    /**
     * @param mixed $cloth_material
     */
    public function setClothMaterial($cloth_material)
    {
        $this->cloth_material = $cloth_material;
    }

}