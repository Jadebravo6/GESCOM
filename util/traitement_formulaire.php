<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

// Connexion à la base de données MySQL
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Créer la base de données si elle n'existe pas
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql_create_db) === TRUE) {
    echo "";
} else {
    echo "Erreur lors de la création de la base de données : " . $conn->error;
}

// Fermer la connexion à la base de données temporairement
$conn->close();

// Se connecter à la base de données nouvellement créée
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Créer la table user si elle n'existe pas
$sql_create_table = "CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    postnom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    sexe VARCHAR(10) NOT NULL,
    lieu_date_naissance VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    classe VARCHAR(100) NOT NULL,
    op VARCHAR(50) NOT NULL,
    portable VARCHAR(20),
    annee_scolaire VARCHAR(50) NOT NULL,
    matricule VARCHAR(100) UNIQUE
)";

if ($conn->query($sql_create_table) === TRUE) {
    echo "";
} else {
    echo "Erreur lors de la création de la table : " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST["nom"];
    $postnom = $_POST["postnom"];
    $prenom = $_POST["prenom"];
    $sexe = $_POST["sexe"];
    $lieu_date_naissance = $_POST["lieu_date_naissance"];
    $adresse = $_POST["adresse"];
    $classe = $_POST["classe"];
    $op = $_POST["op"];
    $portable = isset($_POST["portable"]) ? $_POST["portable"] : "";
    $annee_scolaire = $_POST["annee_scolaire"];

    // Extraire les deux premiers caractères de chaque champ
    $nom_part = substr($nom, 0, 2);
    $postnom_part = substr($postnom, 0, 2);
    $prenom_part = substr($prenom, 0, 2);
    $classe_part = substr($classe, 0, 1);

    // Générer un chiffre identificatif unique de 1 à 99
    $numero_identificatif = mt_rand(1, 99);

    // Générer le matricule en combinant les parties
    $matricule = "{$nom_part}{$postnom_part}{$prenom_part}{$classe_part}{$numero_identificatif}";

    // Vérifier si le matricule existe déjà dans la base de données
    $sql_check_duplicate = "SELECT matricule FROM user WHERE matricule = '$matricule'";
    $result = $conn->query($sql_check_duplicate);

    if ($result->num_rows > 0) {
        // Matricule déjà existant, générer un nouveau chiffre identificatif unique
        $numero_identificatif = mt_rand(1, 99);
        $matricule = "{$nom_part}{$postnom_part}{$prenom_part}{$classe_part}{$numero_identificatif}";
    }

    // Insérer les données dans la base de données
    $sql_insert = "INSERT INTO user (nom, postnom, prenom, sexe, lieu_date_naissance, adresse, classe, op, portable, annee_scolaire, matricule)
                   VALUES ('$nom', '$postnom', '$prenom', '$sexe', '$lieu_date_naissance', '$adresse', '$classe', '$op', '$portable', '$annee_scolaire', '$matricule')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "Données ajoutées avec succès à la base de données.";
    } else {
        echo "Erreur lors de l'ajout des données : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>
