<?php
session_start();

// Assurez-vous que l'utilisateur est connecté avant de continuer
if (!isset($_SESSION["matricule"])) {
    header("Location: ../auth/login_admin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

try {
    $conn = new PDO("mysql:host=$servername;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $createDatabaseSQL = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->exec($createDatabaseSQL);

    $conn->exec("USE $dbname");

    // Création de la table "admin" si elle n'existe pas
    $createAdminTableSQL = "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        postnom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        matricule VARCHAR(20) NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    $conn->exec($createAdminTableSQL);

    // Création de la table "publication" si elle n'existe pas
    $createPublicationTableSQL = "CREATE TABLE IF NOT EXISTS publication (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre_publication VARCHAR(255) NOT NULL,
        contenu_publication TEXT NOT NULL,
        date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        matricule_direction VARCHAR(20) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    $conn->exec($createPublicationTableSQL);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$errors = [];
$confirmationMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_publication'])) {
        // Supprimer la publication
        $publicationId = $_POST['publication_id'];
        try {
            $queryDelete = "DELETE FROM publication WHERE id = :id";
            $stmtDelete = $conn->prepare($queryDelete);
            $stmtDelete->bindParam(':id', $publicationId);
            $stmtDelete->execute();
            $confirmationMessage = 'La publication a été supprimée avec succès. Redirection en cours...';
            
            // Redirection vers publier.php après suppression
            header("Refresh: 2; URL=publier.php"); // Redirige après 2 secondes
            exit();
        } catch (PDOException $e) {
            // Traiter les erreurs de base de données
        }
    } else if (isset($_POST['edit_publication'])) {
        // Redirection vers modifier.php pour modification
        $publicationId = $_POST['publication_id'];
        header("Location: modifier.php?id=$publicationId");
        exit();
    } else {
        // Publier une nouvelle publication
        $titrePublication = htmlspecialchars($_POST["titre_publication"]);
        $contenuPublication = htmlspecialchars($_POST["contenu_publication"]);

        if (empty($titrePublication) || empty($contenuPublication)) {
            $erreur_message = "Les champs Titre et Contenu de la publication sont obligatoires.";
        } else {
            try {
                $queryInsert = "INSERT INTO publication (titre_publication, contenu_publication, matricule_direction) VALUES (:titrePublication, :contenuPublication, :matriculeDirection)";
                $stmtInsert = $conn->prepare($queryInsert);
                $stmtInsert->bindParam(':titrePublication', $titrePublication);
                $stmtInsert->bindParam(':contenuPublication', $contenuPublication);
                $stmtInsert->bindParam(':matriculeDirection', $_SESSION["matricule"]);

                if ($stmtInsert->execute()) {
                    $confirmationMessage = 'La publication a été enregistrée avec succès. Redirection en cours...';
                    
                    // Redirection vers publier.php après publication
                    header("Refresh: 2; URL=publier.php"); // Redirige après 2 secondes
                    exit();
                } else {
                    // Traiter le cas où l'insertion a échoué
                }
            } catch (PDOException $e) {
                // Traiter les erreurs de base de données
            }
        }
    }
}

// Récupérer les publications depuis la base de données
try {
    $query = "SELECT * FROM publication ORDER BY date_publication DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Publier</title>
    <link rel="stylesheet" type="text/css" href="../style/publier.css">
</head>
<body>
    <header>
        <h1>G E S C O M </h1>
        
    </header>
    <h2>        Publier un communiquer </h2>
    <div class="container">

   
        <!-- Afficher le formulaire de publication -->
        <div class="publication-form">
            <form method="post" action="">
                <label for="titre_publication">Titre de la publication:</label><br>
                <input type="text" id="titre_publication" name="titre_publication" required><br>

                <label for="contenu_publication">Contenu de la publication:</label><br>
                <textarea id="contenu_publication" name="contenu_publication" required></textarea><br>

                <input type="submit" value="Publier">
            </form>
            <p></p>

            <a href="logout2.php">Déconnexion</a>
            <!-- Affichage des messages de confirmation ou d'erreur -->
            <?php if (isset($confirmationMessage)) { ?>
                <p><?php echo $confirmationMessage; ?></p>
            <?php } ?>
            <?php if (isset($erreur_message)) { ?>
                <p><?php echo $erreur_message; ?></p>
            <?php } ?>
        </div>

        <!-- Afficher la liste des publications -->
        <div class="publication-list">
            <?php foreach ($publications as $publication) { ?>
                <div class="publication-item">
                    <h3><?php echo $publication['titre_publication']; ?></h3>
                    <p><?php echo substr($publication['contenu_publication'], 0, 100) . (strlen($publication['contenu_publication']) > 100 ? "..." : ""); ?></p>
                    <p class="publication-date">Publié le <?php echo $publication['date_publication']; ?></p>
                    <!-- Boutons de modification et suppression -->
                    <form method="post" action="publier.php">
                        <input type="hidden" name="publication_id" value="<?php echo $publication['id']; ?>">
                        <input type="submit" name="edit_publication" value="Modifier" class="modifier">
                        <input type="submit" name="delete_publication" value="Supprimer" >
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2023 Mon École</p>
    </footer>
</body>
</html>
