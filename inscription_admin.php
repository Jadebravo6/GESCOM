<?php
session_start(); // Démarrer la session


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $postnom = htmlspecialchars($_POST["postnom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $mot_de_passe = $_POST["mot_de_passe"];
    $confirmation_mot_de_passe = $_POST["confirmation_mot_de_passe"];

    if ($mot_de_passe !== $confirmation_mot_de_passe) {
        $erreur_message = "Les mots de passe ne correspondent pas.";
    } else {
        // Générer le matricule pour l'admin (exemple : A2023001)
        $matricule = "A" . date("Y") . str_pad(mt_rand(1, 999), 4, '0', STR_PAD_LEFT);

        $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT); // Hasher le mot de passe

        try {
            $sql = "INSERT INTO admin (nom, postnom, prenom, matricule, mot_de_passe) VALUES (:nom, :postnom, :prenom, :matricule, :mot_de_passe)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':postnom', $postnom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':matricule', $matricule);
            $stmt->bindParam(':mot_de_passe', $hashedPassword);

            if ($stmt->execute()) {
                $confirmationMessage = 'Admin enregistré avec succès.';
            } else {
                $erreur_message = 'Erreur lors de l\'enregistrement de l\'admin.';
            }
        } catch (PDOException $e) {
            $erreur_message = 'Erreur lors de l\'enregistrement de l\'admin : ' . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Inscription Admin</title>
    <link rel="stylesheet" type="text/css" href="signup.css"> <!-- Lien vers le fichier style.css -->
</head>
<body>
    <header>
        <h1>Inscription Admin</h1>
    </header>
    <form method="post" action="">
        <label for="nom">Nom:</label><br>
        <input type="text" id="nom" name="nom" required><br>
        
        <label for="postnom">Post-Nom:</label><br>
        <input type="text" id="postnom" name="postnom" required><br>
        
        <label for="prenom">Prénom:</label><br>
        <input type="text" id="prenom" name="prenom" required><br>
        
        <label for="mot_de_passe">Mot de passe:</label><br>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>
        
        <label for="confirmation_mot_de_passe">Confirmer le mot de passe:</label><br>
        <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required><br>
        
        <input type="submit" value="Inscrire">
    </form>
    <?php if (isset($confirmationMessage)) { ?>
        <p><?php echo $confirmationMessage; ?></p>
    <?php } ?>
    <?php if (isset($erreur_message)) { ?>
        <p><?php echo $erreur_message; ?></p>
    <?php } ?>
    <footer>
        <p>&copy; 2023 Mon École</p>
    </footer>
</body>
</html>
