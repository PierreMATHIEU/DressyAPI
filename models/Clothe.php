<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 17/04/2017
 * Time: 11:04
 */
class Clothe
{
    private $cloth_name;
    private $cloth_color;
    private $cloth_reference;
    private $cloth_urlImage;
    private $cloth_category;
    private $cloth_brand;
    private $cloth_material;
    private $cloth_partner;

    /**
     * Clothe constructor.
     * @param $cloth_name
     * @param $cloth_color
     * @param $cloth_reference
     * @param $cloth_urlImage
     * @param $cloth_category
     * @param $cloth_brand
     * @param $cloth_material
     * @param $cloth_partner
     */
    public function __construct($cloth_name="", $cloth_color="", $cloth_reference=null, $cloth_urlImage=null, $cloth_category=null, $cloth_brand=null, $cloth_material=null, $cloth_partner=null)
    {
        $this->cloth_name = $cloth_name;
        $this->cloth_color = $cloth_color;
        $this->cloth_reference = $cloth_reference;
        $this->cloth_urlImage = $cloth_urlImage;
        $this->cloth_category = $cloth_category;
        $this->cloth_brand = $cloth_brand;
        $this->cloth_material = $cloth_material;
        $this->cloth_partner = $cloth_partner;
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
        return $this->cloth_urlImage;
    }

    /**
     * @param mixed $cloth_urlImage
     */
    public function setClothUrlImage($cloth_urlImage)
    {
        $this->cloth_urlImage = $cloth_urlImage;
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

    /**
     * @return mixed
     */
    public function getClothPartner()
    {
        return $this->cloth_partner;
    }

    /**
     * @param mixed $cloth_partner
     */
    public function setClothPartner($cloth_partner)
    {
        $this->cloth_partner = $cloth_partner;
    }



}