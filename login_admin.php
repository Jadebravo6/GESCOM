<?php
session_start();

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
    $matricule = $_POST["matricule"];
    $mot_de_passe = $_POST["mot_de_passe"];
    
    try {
        $sql = "SELECT matricule, mot_de_passe FROM admin WHERE matricule = :matricule";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
            $_SESSION["matricule"] = $matricule;
            header("Location: publier.php");
            exit();
        } else {
            $erreur_message = "Identifiants incorrects.";
        }
    } catch (PDOException $e) {
        $erreur_message = "Erreur lors de la connexion : " . $e->getMessage();
    }
}


// À la fin de la page, si quelqu'un tente d'accéder via une URL modifiée
if (basename($_SERVER['PHP_SELF']) == 'publier.php' || basename($_SERVER['PHP_SELF']) == 'modifier.php') {
    $_SESSION = array();  // Supprimer toutes les données de la session
    session_destroy();    // Détruire la session
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion Admin</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <header>
        <h1>Connexion Admin</h1>
    </header>
    <form method="post" action="login_admin.php">
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
