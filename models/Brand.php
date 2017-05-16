<?php

/**
 * Created by PhpStorm.
 * User: Piou
 * Date: 20/04/2017
 * Time: 21:56
 */
class Brand
{
    public $id;
    public $libelle;
    public $id_fann;

    /**
     * Brand constructor.
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