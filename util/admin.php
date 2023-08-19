<?php
session_start();

require_once "database.php";

if (isset($_SESSION["matricule"]) && $_SESSION["type_utilisateur"] !== "direction") {
    header("Location: login.php");
    exit();
}

try {
    $sql = "SELECT * FROM communiquer
            ORDER BY date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des publications : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page d'administration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>Page d'administration</h1>
        <?php if (isset($_SESSION["matricule"]) && $_SESSION["type_utilisateur"] === "direction") { ?>
            <p>Connecté en tant que direction : <?php echo $_SESSION["matricule"]; ?></p>
            <a href="publier.php">Publier</a>
            <a href="logout.php">Déconnexion</a>
        <?php } else { ?>
            <a href="login.php">Se connecter</a>
        <?php } ?>
    </header>
   
    <div class="publications">
        <?php foreach ($publications as $publication) { ?>
            <div class="publication">
                <h2><?php echo $publication["titre"]; ?></h2>
                <p><?php echo $publication["contenu"]; ?></p>
                <p>Publié par 
                    <?php
                    $auteur = $publication["auteur"];
                    $type_auteur = $_SESSION["type_utilisateur"];
                    
                    $sql = "SELECT nom FROM $type_auteur WHERE matricule = :matricule_auteur";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':matricule_auteur', $auteur);
                    $stmt->execute();
                    $auteur_nom = $stmt->fetchColumn();
                    
                    echo $auteur_nom . " (" . $_SESSION["type_utilisateur"] . ")";
                    ?>
                    le <?php echo $publication["date"]; ?>
                </p>
                <?php if (isset($_SESSION["matricule"]) && $_SESSION["type_utilisateur"] === "direction") { ?>
                    <a href="modifier.php?id=<?php echo $publication["id"]; ?>">Modifier</a>
                    <a href="supprimer.php?id=<?php echo $publication["id"]; ?>">Supprimer</a>
                <?php } ?>

                <h3>Commentaires :</h3>
                <?php
                $communication_id = $publication["id"];
                try {
                    $sql = "SELECT * FROM commentaires WHERE communication_id = :communication_id ORDER BY date_commentaire ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':communication_id', $communication_id);
                    $stmt->execute();
                    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($commentaires as $commentaire) {
                        $commentateur = $commentaire["matricule_auteur"];
                        $type_commentateur = $_SESSION["type_utilisateur"];
                        
                        $sql = "SELECT nom FROM $type_commentateur WHERE matricule = :matricule_commentateur";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':matricule_commentateur', $commentateur);
                        $stmt->execute();
                        $commentateur_nom = $stmt->fetchColumn();
                        
                        echo "<p><strong>".$commentateur_nom."</strong> le ".$commentaire["date_commentaire"].": ".$commentaire["contenu"]."</p>";
                    }
                } catch (PDOException $e) {
                    echo "Erreur lors de la récupération des commentaires : " . $e->getMessage();
                }
                ?>
            </div>
        <?php } ?>
    </div>

    <script>
        function goToCommentPage(communicationId) {
            window.location.href = "commenter_admin.php?communication_id=" + communicationId;
        }
    </script>
    <footer>
        <p>&copy; 2023 Mon École</p>
    </footer>
</body>
</html>
