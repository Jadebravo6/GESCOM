<?php
// Connexion à la base de données (à adapter en fonction de vos paramètres)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM publication";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json'); // Définit l'en-tête comme JSON
    echo json_encode($publications); // Retourne les publications au format JSON
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
