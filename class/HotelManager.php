<?php

class HotelManager
{

    //connexion bdd

    private $_db;

    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function setDB(PDO $dbh)
    {
        $this->_db = $dbh;
    }


    //Fonction getHotels() renvoie tous les hotels pour hotels.php (liste déroulante)
    public function getHotels()
    {
        $sql = "SELECT id_hotel, nom, adresse FROM hotel";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($rows as $row) {
            $data = [
                'id_hotel' => $row['id_hotel'],
                'nom' => $row['nom'],
                'adresse' => $row['adresse'],
            ];

            $hotel = new Hotel($data);
            $result[] = $hotel;
        }

        return $result;
    }

    //Fonction getHotel($id_hotel) renvoie un hotel, pour retrouver celui que le client a choisi.
    public function getHotel($id_hotel)
    {
        $sql = "SELECT id_hotel, nom, adresse FROM hotel WHERE id_hotel = :id_hotel";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':id_hotel', $id_hotel);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception("Veuillez choisir un hotel.");
        }

        return new Hotel($row);
    }
}
