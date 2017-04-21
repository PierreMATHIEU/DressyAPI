<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 21/04/2017
 * Time: 18:35
 */
class ClotheProperties
{
    private $listBrands=array();
    private $listCategories=array();
    private $listMaterials=array();

    /**
     * ClotheProperties constructor.
     * @param array $listBrands
     * @param array $listCategories
     * @param array $listMaterials
     */
    public function __construct(array $listBrands=array(), array $listCategories=array(), array $listMaterials=array())
    {
        $this->listBrands = $listBrands;
        $this->listCategories = $listCategories;
        $this->listMaterials = $listMaterials;
    }

    /**
     * @return array
     */
    public function getListBrands()
    {
        return $this->listBrands;
    }

    /**
     * @param array $listBrands
     */
    public function setListBrands($listBrands)
    {
        $this->listBrands = $listBrands;
    }

    /**
     * @return array
     */
    public function getListCategories()
    {
        return $this->listCategories;
    }

    /**
     * @param array $listCategories
     */
    public function setListCategories($listCategories)
    {
        $this->listCategories = $listCategories;
    }

    /**
     * @return array
     */
    public function getListMaterials()
    {
        return $this->listMaterials;
    }

    /**
     * @param array $listMaterials
     */
    public function setListMaterials($listMaterials)
    {
        $this->listMaterials = $listMaterials;
    }

}