<?php
// Configuration de la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

// Connexion à MySQL et création de la base de données si elle n'existe pas
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

session_start(); // Démarrer la session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = $_POST["matricule"];
    $mot_de_passe = $_POST["mot_de_passe"];
    
    try {
        $sql = "SELECT matricule, mot_de_passe FROM eleves WHERE matricule = :matricule";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION["matricule"] = $user['matricule']; // Stocker le matricule en session
            
            header("Location: index.php");
            exit();
        } else {
            $erreur_message = "Identifiants incorrects.";
        }
    } catch (PDOException $e) {
        $erreur_message = "Erreur lors de la connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="login.css"> <!-- Lien vers le fichier style.css -->
</head>
<body>
    <header>
        <h1>Connexion</h1>
    </header>
    <form method="post" action="">
        <label for="matricule">Matricule:</label><br>
        <input type="text" id="matricule" name="matricule" required><br>
        
        <label for="mot_de_passe">Mot de passe:</label><br>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>
        
        <input type="submit" value="Se connecter">
    </form>
    <?php if (isset($erreur_message)) { ?>
        <p><?php echo $erreur_message; ?></p>
    <?php } ?>
    <footer>
        <p>&copy; 2023 Mon École</p>
    </footer>
</body>
</html>
