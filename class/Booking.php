<?php

class Booking
{
    protected $id_booking;
    protected $date_debut;
    protected $date_fin;
    protected $date_creation;
    protected $id_client;
    protected $id_chambre;

    public function __construct(array $data)
    {

        $this->setDate_debut($data['date_debut']);
        $this->setDate_fin($data['date_fin']);


        $this->setDate_creation(date('Y-m-d H:i:s'));
    }

    //--- SETTERS

    public function setId_booking($id_booking)
    {
        $this->id_booking = (int) $id_booking;
    }

    public function setDate_debut($date_debut)
    {
        $this->checkDate($date_debut);
        $this->date_debut = $date_debut;
    }

    public function setDate_fin($date_fin)
    {
        $this->checkDate($date_fin);

        // La fin doit etre apres le début (car on reserve au moins une nuit)
        if ($date_fin <= $this->date_debut) {
            throw new Exception("La date de départ doit être postérieure à la date d'arrivée.");
        }

        $this->date_fin = $date_fin;
    }

    public function setDate_creation($date_creation)
    {
        $this->date_creation = $date_creation;
    }

    public function setId_client($id_client)
    {
        $this->id_client = (int) $id_client;
    }

    public function setId_chambre($id_chambre)
    {
        $this->id_chambre = (int) $id_chambre;
    }

    //--- on vérifie quʼune chaine est une vraie date au format AAAA-MM-JJ (nécessaire meme avec un calendrier en HTML = on controle toujours les données recues)
    private function checkDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            throw new Exception("La date '$date' nʼest pas valide.");
        }
    }

    //--- GETTERS

    public function getId_booking()
    {
        return $this->id_booking;
    }

    public function getDate_debut()
    {
        return $this->date_debut;
    }

    public function getDate_fin()
    {
        return $this->date_fin;
    }

    public function getDate_creation()
    {
        return $this->date_creation;
    }

    public function getId_client()
    {
        return $this->id_client;
    }

    public function getId_chambre()
    {
        return $this->id_chambre;
    }
}
