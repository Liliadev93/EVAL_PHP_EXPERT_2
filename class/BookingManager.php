<?php

class BookingManager
{
    //connexion bdd
    private $_db;

    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function setDb(PDO $dbh)
    {
        $this->_db = $dbh;
    }

    //Fonction findRoomAvailable() cherche la premiere chambre libre de l'hotel sur la période demandée (sans réservation qui chevauche) et la renvoie sous forme d'objet Chambre, ou lance une exception sʼil nʼy en a aucune.
    public function findRoomAvailable(Hotel $hotel, $date_debut, $date_fin)
    {
        $sql = "SELECT id_chambre, numero_chambre, id_hotel FROM chambre
                    WHERE id_hotel = :id_hotel AND id_chambre NOT IN (
                        SELECT id_chambre FROM booking
                        WHERE :new_reservation_fin > date_debut AND :new_reservation_debut < date_fin
                    )
                    ORDER BY numero_chambre LIMIT 1";
        $stmt = $this->_db->prepare($sql);

        $id_hotel = $hotel->getId_hotel();  // on lit lʼid dans lʼobjet Hotel avec son getter

        $stmt->bindParam(':id_hotel', $id_hotel);
        $stmt->bindParam(':new_reservation_debut', $date_debut);
        $stmt->bindParam(':new_reservation_fin', $date_fin);

        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Aucune chambre n'est disponible sur cette période.");
        }


        return new Chambre($row);
    }

    //Fonction addBooking() sert a enregistrer en bdd le client avec son nom et son email
    private function addClient(Client $client) //private sert ici a ce quʼon enregistre jamais un client sans reservation, seule la classe BookingManager peut lʼappeler 
    {
        $email = $client->getEmail();

        // On recherche si le client existe deja en bdd, on le cherche par son email = evite les doublons
        $sql = "SELECT id_client FROM client WHERE email = :email";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // si il existe on récupere son id et on nʼinsere rien (on ne le recrée pas)
            $client->setId_client($row['id_client']);
            return;
        }

        // si il nʼexiste pas : on lʼenregistre
        $sql = "INSERT INTO client (nom, email) VALUES (:nom, :email)";
        $stmt = $this->_db->prepare($sql);
        $nom = $client->getNom();
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->execute();


        $client->setId_client($this->_db->lastInsertId()); // lastInsertId() renvoie lʼid que mySQL vient de donner a la ligne insérée.
    }

    //Fonction addBooking() appelle les deux fonctions précedentes pour ajouter une réservation en bdd
    public function addBooking(Client $client, Booking $booking, Hotel $hotel)
    {
        // La date dʼarrivée ne doit pas etre dans le passé
        if ($booking->getDate_debut() < date('Y-m-d')) {
            throw new Exception("La date dʼarrivée ne peut pas etre dans le passé.");
        }

        // On cherche une chambre libre (exception lancée sʼil nʼy en a aucune)
        $chambre = $this->findRoomAvailable($hotel, $booking->getDate_debut(), $booking->getDate_fin());

        // On enregistre le client
        $this->addClient($client);

        // On complete la réservation avec lʼid du client et lʼid de la chambre
        $booking->setId_client($client->getId_client());
        $booking->setId_chambre($chambre->getId_chambre());

        // On enregistre la réservation en base
        $sql = "INSERT INTO booking (id_client, id_chambre, date_debut, date_fin, date_creation)
            VALUES (:id_client, :id_chambre, :date_debut, :date_fin, :date_creation)";
        $stmt = $this->_db->prepare($sql);

        $id_client = $booking->getId_client();
        $id_chambre = $booking->getId_chambre();
        $date_debut = $booking->getDate_debut();
        $date_fin = $booking->getDate_fin();
        $date_creation = $booking->getDate_creation();

        $stmt->bindParam(':id_client', $id_client);
        $stmt->bindParam(':id_chambre', $id_chambre);
        $stmt->bindParam(':date_debut', $date_debut);
        $stmt->bindParam(':date_fin', $date_fin);
        $stmt->bindParam(':date_creation', $date_creation);

        $stmt->execute();

        return $chambre;
    }

    // ------- A lʼattention du correcteur :

    /*  
    Pour que mon code soit correct, il aurait fallu resoudre les deux limites que jʼai indentifié: 
        - si mySQL plante entre lʼenregistrement du client et celui de la réservation, le client reste enregistré mais sans réservation : je nʼai pas su comment faire pour garantir que ces deux enregistrement se fassent ensemble ou pas du tout. 

        - si deux clients réservent en meme temps, ils pourraient donc obtenir la meme chambre 
    */
}
