<?php

class Hotel
{
    protected $id_hotel;
    protected $nom;
    protected $adresse;

    public function __construct(array $data)
    {
        $this->setId_hotel($data['id_hotel']);
        $this->setNom($data['nom']);
        $this->setAdresse($data['adresse']);
    }

    //--- SETTERS

    public function setId_hotel($id_hotel)
    {
        $this->id_hotel = (int) $id_hotel;
    }

    public function setNom($nom)
    {

        $this->nom = $nom;
    }

    public function setAdresse($adresse)
    {

        $this->adresse = $adresse;
    }

    //--- GETTERS
    public function getId_hotel()
    {
        return $this->id_hotel;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }
}
