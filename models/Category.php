<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 20/04/2017
 * Time: 21:58
 */
class Category
{
    public $id;
    public $libelle;
    public $id_fann;

    /**
     * Category constructor.
     * @param $id
     * @param $libelle
     * @param $id_fann
     */
    public function __construct($id=0, $libelle="", $id_fann=0)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        $this->id_fann = $id_fann;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return mixed
     */
    public function getIdFann()
    {
        return $this->id_fann;
    }

    /**
     * @param mixed $id_fann
     */
    public function setIdFann($id_fann)
    {
        $this->id_fann = $id_fann;
    }




}