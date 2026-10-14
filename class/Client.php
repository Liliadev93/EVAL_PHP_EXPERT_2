<?php

class Client
{
    protected $id_client;
    protected $nom;
    protected $email;

    public function __construct(array $data)
    {
        $this->setNom($data['nom']);
        $this->setEmail($data['email']);
    }

    //--- SETTERS

    public function setId_client($id_client)
    {
        $this->id_client = (int) $id_client;
    }

    public function setNom($nom)
    {
        $nom = trim($nom);

        if (empty($nom)) {
            throw new Exception("Le nom est obligatoire.");
        }

        $this->nom = $nom;
    }

    public function setEmail($email)
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //filter_var() applique un filtre a une valeur : FILTER_VALIDATE_EMAIL vérifie que cʼest une adresse email valide
            throw new Exception("Votre email nʼest pas valide");
        }

        $this->email = $email;
    }

    //--- GETTERS
    public function getId_client()
    {
        return $this->id_client;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
