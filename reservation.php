<?php
require_once('config.php');
require_once('class/Client.php');
require_once('class/Hotel.php');
require_once('class/Chambre.php');
require_once('class/Booking.php');
require_once('class/HotelManager.php');
require_once('class/BookingManager.php');

$hotels = [];
$erreur = null;
$succes = null;

// Hotel présélectionné: celui du formulaire envoyé, sinon celui du lien hotels.php?id_hotel=...
$id_hotel_choisi = $_POST['id_hotel'] ?? $_GET['id_hotel'] ?? '';

try {
    // Connexion bdd
    $dbh = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] //erreurs deviennent exceptions
    );

    $hotelManager = new HotelManager($dbh);
    $bookingManager = new BookingManager($dbh);

    // Liste des hotels pour la liste déroulante
    $hotels = $hotelManager->getHotels();

    // ---- TRAITEMENT DU FORMULAIRE ----
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


        $hotel = $hotelManager->getHotel((int) $id_hotel_choisi);

        // Les setters des classes valident les données et lancent une exception si elles sont invalides
        $client = new Client([
            'nom' => $_POST['nom'] ?? '',
            'email' => $_POST['email'] ?? '',
        ]);

        $booking = new Booking([
            'date_debut' => $_POST['date_debut'] ?? '',
            'date_fin' => $_POST['date_fin'] ?? '',
        ]);

        // Réservation: renvoie la chambre attribuée
        $chambre = $bookingManager->addBooking($client, $booking, $hotel);

        $succes = "Réservation confirmée : chambre " . $chambre->getNumero_chambre()
            . " a lʼ" . $hotel->getNom()
            . ", du " . date('d/m/Y', strtotime($booking->getDate_debut()))
            . " au " . date('d/m/Y', strtotime($booking->getDate_fin())) . ".";
    }
} catch (PDOException $e) {
    // Erreur technique: on nʼaffiche pas le détail SQL au visiteur
    $erreur = "Une erreur technique est survenue, la réservation nʼa pas été enregistrée.";
} catch (Exception $e) {
    // Erreur de validation, hotel invalide ou aucune chambre libre : le message vient des classes
    $erreur = $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Booking - Réservation</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>

    <?php include('includes/menu.html'); ?>

    <h1>Réserver une chambre</h1>

    <?php if ($succes): ?>
        <p class="succes"><?php echo htmlspecialchars($succes); ?></p>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
    <?php endif; ?>

    <form method="post" class="formulaire">

        <label for="id_hotel">Hotel</label>
        <select name="id_hotel" id="id_hotel">
            <option value="">-- Choisir un hotel --</option>
            <?php foreach ($hotels as $h): ?>
                <option value="<?php echo $h->getId_hotel(); ?>"
                    <?php echo ($h->getId_hotel() == $id_hotel_choisi) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($h->getNom()); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label for="date_debut">Date dʼarrivée</label>
        <input type="date" name="date_debut" id="date_debut" min="<?php echo date('Y-m-d'); ?>"
            value="<?php echo htmlspecialchars($_POST['date_debut'] ?? ''); ?>">

        <label for="date_fin">Date de départ</label>
        <input type="date" name="date_fin" id="date_fin" min="<?php echo date('Y-m-d'); ?>"
            value="<?php echo htmlspecialchars($_POST['date_fin'] ?? ''); ?>">

        <input type="submit" value="Réserver">
    </form>

</body>

</html>