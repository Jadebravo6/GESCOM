<?php
session_start();

// Assurez-vous que l'utilisateur est connecté avant de continuer
if (!isset($_SESSION["matricule"])) {
    header("Location: login_admin.php");
    exit();
}

$authorizedMatricules = array();  // Liste des matricules autorisés

// Récupérer les matricules autorisés depuis la table admin
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $queryAuthorizedMatricules = "SELECT matricule FROM admin";
    $stmtAuthorizedMatricules = $conn->prepare($queryAuthorizedMatricules);
    $stmtAuthorizedMatricules->execute();
    $resultAuthorizedMatricules = $stmtAuthorizedMatricules->fetchAll(PDO::FETCH_COLUMN);
    $authorizedMatricules = $resultAuthorizedMatricules;
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if (!in_array($_SESSION["matricule"], $authorizedMatricules)) {
    header("Location: login_admin.php");  // Rediriger si le matricule n'est pas autorisé
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: publier.php");
    exit();
}

$publicationId = $_GET['id'];

try {
    $querySelect = "SELECT * FROM publication WHERE id = :publicationId";
    $stmtSelect = $conn->prepare($querySelect);
    $stmtSelect->bindParam(':publicationId', $publicationId);
    $stmtSelect->execute();
    $publicationToEdit = $stmtSelect->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {
    if (isset($_POST["new_titre_publication"]) && isset($_POST["new_contenu_publication"])) {
        // Récupérer les données du formulaire
        $newTitrePublication = htmlspecialchars($_POST["new_titre_publication"]);
        $newContenuPublication = htmlspecialchars($_POST["new_contenu_publication"]);

        // Mettre à jour la publication dans la base de données
        try {
            $queryUpdate = "UPDATE publication SET titre_publication = :newTitre, contenu_publication = :newContenu WHERE id = :publicationId";
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(':newTitre', $newTitrePublication);
            $stmtUpdate->bindParam(':newContenu', $newContenuPublication);
            $stmtUpdate->bindParam(':publicationId', $publicationId);
            $stmtUpdate->execute();

            header("Location: publier.php"); // Rediriger vers publier.php après la modification
            exit();
        } catch (PDOException $e) {
            // Traiter les erreurs de base de données
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier la publication</title>
    <link rel="stylesheet" type="text/css" href="modifier.css">
    
</head>
<body>
    <header>
        <h1>Modifier la publication</h1>
        <!-- Bouton de déconnexion -->
    </header>
    <div class="container">
        <div class="publication-form">
            <form method="post" action="">
                <input type="hidden" name="publication_id" value="<?php echo $publicationToEdit['id']; ?>">
                <label for="new_titre_publication">Nouveau titre de la publication:</label><br>
                <input type="text" id="new_titre_publication" name="new_titre_publication" value="<?php echo isset($publicationToEdit['titre_publication']) ? $publicationToEdit['titre_publication'] : ''; ?>" required><br>
                
                <label for="new_contenu_publication">Nouveau contenu de la publication:</label><br>
                <textarea id="new_contenu_publication" name="new_contenu_publication" required><?php echo isset($publicationToEdit['contenu_publication']) ? $publicationToEdit['contenu_publication'] : ''; ?></textarea><br>
                
                <input type="submit" name="save_changes" value="Enregistrer">
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2023 Mon École</p>
    </footer>
</body>
</html>
