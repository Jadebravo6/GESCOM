<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["matricule"])) {
    header("Location: login.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$matricule = $_SESSION["matricule"];

// Gérer la déconnexion
if (isset($_POST["deconnexion"])) {
    session_unset(); // Supprimer toutes les variables de session
    session_destroy(); // Détruire la session
    header("Location: login.php"); // Rediriger vers la page de connexion après la déconnexion
    exit();
}

// Récupérer les publications depuis la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM publication ORDER BY date_publication DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer les informations de l'utilisateur connecté depuis la table "eleves"
try {
    $sql = "SELECT nom, postnom, prenom FROM eleves WHERE matricule = :matricule";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':matricule', $matricule);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Traiter les erreurs de base de données
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Lien vers le fichier style.css -->
</head>
<body>
<meta http-equiv="refresh" content="30"> <!-- Actualise la page toutes les 30 secondes -->
    <header>
        <h1>G E S C O M</h1>
    </header>
    <main>

    <div class="dash">

        <p>Vous êtes connecté en tant que : <?php echo $user['nom'] . ' ' . $user['postnom'] . ' ' . $user['prenom']; ?></p>
        <form method="post">
         
            <input type="submit" name="deconnexion" value="Se déconnecter" div class="deconnexion">
          
        </form>
    
    </div>
        

        <h2>Valve de Communiquer </h2>
        <ul class="publication-list">
            <?php foreach ($publications as $publication) { ?>
                <li class="publication-item">
                    <div class="publication-preview">
                        <a href="full.php?id=<?php echo $publication['id']; ?>" class="publication-link">
                            <strong><?php echo $publication['titre_publication']; ?></strong><br>
                            <?php echo substr($publication['contenu_publication'], 0, 20) . (strlen($publication['contenu_publication']) > 20 ? "..." : ""); ?>
                        </a>
                    </div>
                    <span class="publication-date">Publié le <?php echo $publication['date_publication']; ?></span>
                </li>
            <?php } ?>
        </ul>
    </main>
    <footer>
        <p>&copy; Ecole Gescom</p>
    </footer>
</body>
</html>


<script>
    // Récupérer les éléments nécessaires
const publicationItems = document.querySelectorAll('.publication-item');
const modal = document.querySelector('.modal');
const modalContent = document.querySelector('.modal-content');

// Ajouter un gestionnaire de clic à chaque publication
publicationItems.forEach(item => {
    const preview = item.querySelector('.publication-preview');
    const fullContent = item.querySelector('.publication-full').innerHTML;
    item.addEventListener('click', () => {
        modalContent.innerHTML = fullContent;
        modal.style.display = 'flex';
    });
    preview.addEventListener('click', (e) => e.stopPropagation());
});

// Fermer la fenêtre modale lorsqu'on clique à l'extérieur de son contenu
modal.addEventListener('click', () => modal.style.display = 'none');
modalContent.addEventListener('click', (e) => e.stopPropagation());



function updatePublicationList() {
    const publicationList = document.getElementById('publication-list');
    publicationList.innerHTML = ''; // Réinitialise la liste

    // Utiliser AJAX pour récupérer les données JSON des publications
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const publications = JSON.parse(xhr.responseText);
            publications.forEach(publication => {
                const listItem = document.createElement('li');
                listItem.className = 'publication-item';
                listItem.innerHTML = `
                    <div class="publication-preview">
                        <a href="full.php?id=${publication.id}" class="publication-link">
                            <strong>${publication.titre_publication}</strong><br>
                            ${publication.contenu_publication.substring(0, 20) + (publication.contenu_publication.length > 20 ? "..." : "")}
                        </a>
                    </div>
                    <span class="publication-date">Publié le ${publication.date_publication}</span>
                `;
                publicationList.appendChild(listItem);
            });
        }
    };
    xhr.open('GET', 'get_publications.php', true);
    xhr.send();
}

// Mettre à jour la liste des publications au chargement initial
updatePublicationList();

// Mettre à jour automatiquement la liste des publications toutes les 5 secondes
setInterval(updatePublicationList, 1000); // Actualise toutes les 5 secondes (5000 millisecondes)
</script>