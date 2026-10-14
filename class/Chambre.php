<?php

class Chambre
{
    protected $id_chambre;
    protected $numero_chambre;
    protected $id_hotel;

    public function __construct(array $data)
    {
        $this->setId_chambre($data['id_chambre']);
        $this->setNumero_chambre($data['numero_chambre']);
        $this->setId_hotel($data['id_hotel']);
    }

    //--- SETTERS

    public function setId_chambre($id_chambre)
    {
        $this->id_chambre = (int) $id_chambre;
    }

    public function setNumero_chambre($numero_chambre)
    {
        $this->numero_chambre = (int) $numero_chambre;
    }

    public function setId_hotel($id_hotel)
    {
        $this->id_hotel = (int) $id_hotel;
    }

    //--- GETTERS

    public function getId_chambre()
    {
        return $this->id_chambre;
    }

    public function getNumero_chambre()
    {
        return $this->numero_chambre;
    }

    public function getId_hotel()
    {
        return $this->id_hotel;
    }
}
