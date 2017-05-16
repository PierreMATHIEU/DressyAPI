<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 02/05/2017
 * Time: 10:28
 */
class Color
{
    public $id;
    public $libelle;
    public $id_fann;

    /**
     * Color constructor.
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return int
     */
    public function getIdFann()
    {
        return $this->id_fann;
    }

    /**
     * @param int $id_fann
     */
    public function setIdFann($id_fann)
    {
        $this->id_fann = $id_fann;
    }


}