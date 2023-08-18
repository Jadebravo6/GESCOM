<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="sign.css">
</head>
<body>


<?php
// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet_ecole";

$errors = [];
$confirmationMessage = "";
$matriculeGenerated = "";

// Connexion à MySQL et création de la base de données si elle n'existe pas
try {
    $pdo = new PDO("mysql:host=$servername;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $createDatabaseSQL = "CREATE DATABASE IF NOT EXISTS $dbname";
    $pdo->exec($createDatabaseSQL);

    $pdo->exec("USE $dbname");
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Création de la table si elle n'existe pas
try {
    $createTableSQL = "CREATE TABLE IF NOT EXISTS eleves (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        postnom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        sexe VARCHAR(1) NOT NULL,
        lieu_date_naissance VARCHAR(255) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        portable VARCHAR(20) NOT NULL,
        classe INT NOT NULL,
        op VARCHAR(255) NOT NULL,
        annee_scolaire INT NOT NULL,
        matricule VARCHAR(20) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $pdo->exec($createTableSQL);
} catch (PDOException $e) {
    die("Erreur lors de la création de la table : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation et nettoyage des données
    $nom = htmlspecialchars($_POST["nom"]);
    if (empty($nom)) {
        $errors['nom'] = 'Le champ Nom est obligatoire.';
    }

    $postnom = htmlspecialchars($_POST["postnom"]);
    if (empty($postnom)) {
        $errors['postnom'] = 'Le champ Post-Nom est obligatoire.';
    }

    $prenom = htmlspecialchars($_POST["prenom"]);
    if (empty($prenom)) {
        $errors['prenom'] = 'Le champ Prénom est obligatoire.';
    }

    $sexe = $_POST["sexe"];
    if (empty($sexe)) {
        $errors['sexe'] = 'Le champ Sexe est obligatoire.';
    }

    $lieuDateNaissance = htmlspecialchars($_POST["lieu_date_naissance"]);
    if (empty($lieuDateNaissance)) {
        $errors['lieu_date_naissance'] = 'Le champ Lieu et date de naissance est obligatoire.';
    }

    $adresse = htmlspecialchars($_POST["adresse"]);
    if (empty($adresse)) {
        $errors['adresse'] = 'Le champ Adresse est obligatoire.';
    }

    $portable = htmlspecialchars($_POST["portable"]);
    if (empty($portable)) {
        $errors['portable'] = 'Le champ Portable est obligatoire.';
    }

    $classe = $_POST["classe"];
    if (empty($classe)) {
        $errors['classe'] = 'Le champ Choix de la classe est obligatoire.';
    }

    $op = $_POST["op"];
    if (empty($op)) {
        $errors['op'] = 'Le champ Choix de l\'option est obligatoire.';
    }

    $anneeScolaire = htmlspecialchars($_POST["annee_scolaire"]);
    if (empty($anneeScolaire)) {
        $errors['annee_scolaire'] = 'Le champ Année scolaire est obligatoire.';
    } elseif (!preg_match("/^\d{4}-\d{4}$/", $anneeScolaire)) {
        $errors['annee_scolaire'] = 'Le format de l\'année scolaire doit être "AAAA-AAAA".';
    }


     // Validation et nettoyage du mot de passe
     $motDePasse = $_POST["mot_de_passe"];
     $confirmationMotDePasse = $_POST["confirmation_mot_de_passe"];
     
     if (empty($motDePasse)) {
         $errors['mot_de_passe'] = 'Le champ Mot de passe est obligatoire.';
     } elseif ($motDePasse !== $confirmationMotDePasse) {
         $errors['mot_de_passe'] = 'Les mots de passe ne correspondent pas.';
     } else {
         // Hasher le mot de passe
         $hashedMotDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
     }



    // Vérification du numéro de téléphone pour éviter les doublons
    $queryCheckPhone = "SELECT COUNT(*) as count FROM eleves WHERE portable = :portable";
    $stmtCheckPhone = $pdo->prepare($queryCheckPhone);
    $stmtCheckPhone->bindParam(':portable', $portable);
    $stmtCheckPhone->execute();
    $resultCheckPhone = $stmtCheckPhone->fetch(PDO::FETCH_ASSOC);

    if ($resultCheckPhone['count'] > 0) {
        $errors['portable'] = 'Ce numéro de téléphone est déjà utilisé.';
    }

    // Vérification de l'année scolaire
    $anneeActuelle = date("Y");
    if ($anneeScolaire < $anneeActuelle) {
        $errors['annee_scolaire'] = 'L\'année scolaire ne peut pas être inférieure à l\'année actuelle.';
    }

    // Si des erreurs sont présentes, n'exécutez pas l'insertion
    if (empty($errors)) {
        // Générer le matricule
        $matricule = strtoupper(substr($nom, 0, 3)) . strtoupper(substr($postnom, 0, 1)) . strtoupper(substr($prenom, 0, 1)) . substr($classe, 0, 1) . mt_rand(100, 999);

        // Hasher le mot de passe (à ajouter)
        // ...

       // Insérer les données dans la base de données
       $queryInsert = "INSERT INTO eleves (nom, postnom, prenom, sexe, lieu_date_naissance, adresse, portable, classe, op, annee_scolaire, matricule, mot_de_passe) VALUES (:nom, :postnom, :prenom, :sexe, :lieuDateNaissance, :adresse, :portable, :classe, :op, :anneeScolaire, :matricule, :motDePasse)";
       $stmtInsert = $pdo->prepare($queryInsert);
       $stmtInsert->bindParam(':nom', $nom);
       $stmtInsert->bindParam(':postnom', $postnom);
       $stmtInsert->bindParam(':prenom', $prenom);
       $stmtInsert->bindParam(':sexe', $sexe);
       $stmtInsert->bindParam(':lieuDateNaissance', $lieuDateNaissance);
       $stmtInsert->bindParam(':adresse', $adresse);
       $stmtInsert->bindParam(':portable', $portable);
       $stmtInsert->bindParam(':classe', $classe);
       $stmtInsert->bindParam(':op', $op);
       $stmtInsert->bindParam(':anneeScolaire', $anneeScolaire);
       $stmtInsert->bindParam(':matricule', $matricule);
       $stmtInsert->bindParam(':motDePasse', $hashedMotDePasse); // Lier le paramètre mot_de_passe ici

        if ($stmtInsert->execute()) {
            $confirmationMessage = 'Les données ont été enregistrées avec succès.';
            $matriculeGenerated = $matricule;
        } else {
            // Traiter le cas où l'insertion a échoué
        }
    }
}
?>

<div class="formulaire">
    <form action="inscription.php" method="post">
        <div class="group-1">
            <table>
                <tr>
                    <td><input type="text" name="nom" placeholder="Nom" required></td>
                    <td><span class="error"><?php echo $errors['nom'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td><input type="text" name="postnom" placeholder="Post-Nom" required></td>
                    <td><span class="error"><?php echo $errors['postnom'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td><input type="text" name="prenom" placeholder="Prénom" required></td>
                    <td><span class="error"><?php echo $errors['prenom'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td>
                        <select name="sexe">
                            <option hidden>Sexe</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </td>
                    <td><span class="error"><?php echo $errors['sexe'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td><input type="text" name="lieu_date_naissance" placeholder="Lieu et date de naissance" required></td>
                    <td><span class="error"><?php echo $errors['lieu_date_naissance'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td><input type="text" name="adresse" placeholder="Adresse" required></td>
                    <td><span class="error"><?php echo $errors['adresse'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td><input type="text" name="portable" placeholder="Portable" required></td>
                    <td><span class="error"><?php echo $errors['portable'] ?? ''; ?></span></td>
                </tr>


                <tr>
                <td><input type="password" name="mot_de_passe" placeholder="Mot de passe" required></td>
                <td><span class="error"><?php echo $errors['mot_de_passe'] ?? ''; ?></span></td>
            </tr>
            <tr>
                <td><input type="password" name="confirmation_mot_de_passe" placeholder="Confirmer le mot de passe" required></td>
                <td></td>
            </tr>
                

            </table>
        </div>
        <div class="group-2">
            <table>
                <tr>
                    <td>
                        <select name="classe">
                            <option hidden>Choix de la classe</option>
                            <option value="1">1er secondaire</option>
                            <option value="2">2ème secondaire</option>
                            <option value="3">3ème umanité</option>
                            <option value="4">4ème umanité</option>
                            <option value="5">5ème umanité</option>
                            <option value="6">6ème umanité</option>
                        </select>
                    </td>
                    <td><span class="error"><?php echo $errors['classe'] ?? ''; ?></span></td>
                </tr>
                <tr>
                    <td>
                        <select name="op">
                            <option hidden>Choix de l'option</option>
                            <option value="scientifique">Scientifique</option>
                            <option value="commercial">Commercial</option>
                            <option value="latin_philo">Latin-Philo</option>
                            <option value="coupe_et_couture">Coupe et Couture</option>
                            <option value="mecanique">Mécanique</option>
                            <option value="electricite">Électricité</option>
                            <option value="hotelerie">Hôtelerie</option>
                            <option value="biochimie">Biochimie</option>
                            <option value="mathphysique">Math-Physique</option>
                        </select>
                    </td>
                    <td><span class="error"><?php echo $errors['op'] ?? ''; ?></span></td>
                </tr>
            </table>
        </div>
        <div class="group-3">
            <table>
                <!-- ... (vos autres champs du groupe 3) ... -->
            </table>
            <table>
            <tr>
                <td><input type="text" name="annee_scolaire" placeholder="Année scolaire (ex: 2023-2024)" required></td>
                <td><span class="error"><?php echo $errors['annee_scolaire'] ?? ''; ?></span></td>
            </tr>
                        </table>
            <input type="submit" value="Envoyer le formulaire">
        </div>
    </form>
</div>

<?php
if (!empty($confirmationMessage)) {
    echo "<p>$confirmationMessage</p>";
    if (!empty($matriculeGenerated)) {
        echo "<p>Matricule généré : $matriculeGenerated</p>";
    }
}

// Afficher les messages d'erreur
if (!empty($errors)) {
    echo "<div class='error-container'>";
    echo "<p>Erreurs :</p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}
?>

</body>
</html>