<?php
require_once('config.php');
require_once('class/Hotel.php');
require_once('class/HotelManager.php');

$hotels = [];
$erreur = null;

try {
    // Connexion bdd 
    $dbh = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] //les erreurs SQL deviennent des exceptions
    );

    $hotelManager = new HotelManager($dbh);
    $hotels = $hotelManager->getHotels();
} catch (PDOException $e) {
    $erreur = "Impossible de récupérer la liste des hotels pour le moment.";
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Booking - Nos hotels</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>

    <?php include('includes/menu.html'); ?>

    <h1>Nos hotels</h1>

    <?php if ($erreur): ?>
        <p class="erreur"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <?php foreach ($hotels as $hotel): ?>
        <div class="hotel">
            <h2><?php echo htmlspecialchars($hotel->getNom()); ?></h2>
            <p><?php echo htmlspecialchars($hotel->getAdresse()); ?></p>
            <a href="reservation.php?id_hotel=<?php echo $hotel->getId_hotel(); ?>">Réserver</a>

        </div>
    <?php endforeach; ?>

</body>

</html>