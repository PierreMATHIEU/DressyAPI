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

    /**
     * Category constructor.
     * @param $id
     * @param $libelle
     */
    public function __construct($id=0, $libelle="")
    {
        $this->id = $id;
        $this->libelle = $libelle;
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


}