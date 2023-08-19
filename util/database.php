<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

try {
    $pdo = new PDO("mysql:host=$servername;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $createDatabaseSQL = "CREATE DATABASE IF NOT EXISTS $dbname";
    $pdo->exec($createDatabaseSQL);

    $pdo->exec("USE $dbname");
    
    

    // Création de la table "admin" si elle n'existe pas
    $createAdminTableSQL = "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        postnom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        matricule VARCHAR(20) NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    $pdo->exec($createAdminTableSQL);

    // Création de la table "publication" si elle n'existe pas
    $createPublicationTableSQL = "CREATE TABLE IF NOT EXISTS publication (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contenu TEXT NOT NULL,
        date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        auteur VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    $pdo->exec($createPublicationTableSQL);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
