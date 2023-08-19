<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/login.css">
</head>
<body class="align">


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

    <style>
        .go {
            display: flex;
            width: 79%;
            text-align: center;
            justify-content: center;
            justify-self: center;
        }
        .identite, .coordonnees, .connexion {
            display: flex;
            justify-content: center;
            width: 100vh;
        }
    </style>

    <div class="align">
        <div>
            <h1 class="align">Inscription</h1>

            <form action="" method="POST" class="form login">
                <section class="identite form">
                    <div class="form">
                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                            </label>
                            <input type="text" class="form__input" name="nom" placeholder="Nom" required>
                        </div>

                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                            </label>
                            <input type="text" class="form__input" name="prenom" placeholder="Prénom" required>
                        </div>
                    </div>

                    <div class="form">
                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                            </label>
                            <input type="text" class="form__input" name="postnom" placeholder="Post-nom" required>
                        </div>

                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                            </label>
                            <select class="form__input" name="sexe">
                                <option hidden>Sexe</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                    </div>
                </section>
                
                <section class="coordonnees form">
                    <div class="form">
                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-calendar"></use></svg>
                            </label>
                            <input type="text" class="form__input" name="lieu_date_naissance" placeholder="Lieu et Date de naissance" required>
                        </div>

                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-location"></use></svg>
                            </label>
                            <input type="text" class="form__input" name="adresse" placeholder="Adresse d'habitation" required>
                        </div>

                        <div class="form__field">
                            <label>
                                <svg class="icon"><use xlink:href="#icon-phone"></use></svg>
                            </label>
                            <input type="text" class="form__input" name="portable" placeholder="Portable" required>
                        </div>
                    </div>

                    <div class="form">
                        <div class="form">
                            <div class="form__field">
                                    <label>
                                        <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                                    </label>
                                    <select class="form__input" name="classe">
                                        <option hidden>Classe</option>
                                        <option value="1">1er Secondaire</option>
                                        <option value="2">2ème Secondaire</option>
                                        <option value="3">3ème des Humanités</option>
                                        <option value="4">4ème des Humanités</option>
                                        <option value="5">5ème des Humanités</option>
                                        <option value="6">6ème des Humanités</option>
                                    </select>
                            </div>

                            <div class="form__field">
                                <label>
                                    <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                                </label>
                                <select class="form__input" name="op">
                                    <option hidden>Option</option>
                                    <option value="scientifique">Scientifique</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="latin_philo">Latin-Philo</option>
                                    <option value="coupe_et_couture">Coupe et Couture</option>
                                    <option value="mecanique">Mécanique</option>
                                    <option value="electricite">Électricité</option>
                                    <option value="hotelerie">Hôtelerie</option>
                                    <option value="biochimie">Bio-Chimie</option>
                                    <option value="mathphysique">Math-Physique</option>
                                </select>
                            </div>

                            <div class="form__field">
                                <label>
                                    <svg class="icon"><use xlink:href="#icon-calendar"></use></svg>
                                </label>
                                <input type="text" class="form__input" name="annee_scolaire" placeholder="Année scolaire (ex: 2023-2024)" required>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="connexion form">
                    <div class="form__field">
                        <label>
                            <svg class="icon"><use xlink:href="#icon-lock"></use></svg>
                        </label>
                        <input type="password" class="form__input" name="mot_de_passe" placeholder="Mot de passe" required>
                        <span class="error"><?php echo $errors['mot_de_passe'] ?? ''; ?></span>
                    </div>

                    <div class="form__field">
                        <label>
                            <svg class="icon"><use xlink:href="#icon-lock"></use></svg>
                        </label>
                        <input type="password" class="form__input" name="confirmation_mot_de_passe" placeholder="Confirmer le mot de passe" required>
                    </div>
                </section>

                <input class="go" type="submit" value="Envoyer le formulaire">

                <p class="text--center">Vous avez déjà un compte? <a href="login.php">Connectez-vous.</a> 
                    <svg class="icon">
                        <use xlink:href="#icon-arrow-right"></use>
                    </svg>
                </p>
            </form>
        </div>

        <div class="align">
            <p>
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
            </p>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 Mon École</p>
    </footer>

    <svg xmlns="http://www.w3.org/2000/svg" class="icons">
        <symbol id="icon-arrow-right" viewBox="0 0 1792 1792">
            <path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z" />
        </symbol>
        <symbol id="icon-lock" viewBox="0 0 1792 1792">
            <path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z" />
        </symbol>
        <symbol id="icon-user" viewBox="0 0 1792 1792">
            <path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z" />
        </symbol>
        <symbol id="icon-location" viewBox="0 0 384 512">
            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. -->
            <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
        </symbol>
        <symbol id="icon-phone" viewBox="0 0 512 512">
            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. -->
            <path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>
        </symbol>
        <symbol id="icon-calendar" viewBox="0 0 448 512">
            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. -->
            <path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64C28.7 64 0 92.7 0 128v16 48V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V192 144 128c0-35.3-28.7-64-64-64H344V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192H400V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192z"/>
        </symbol>
    </svg>
</body>
</html>