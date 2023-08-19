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
    <link rel="stylesheet" type="text/css" href="../style/login.css"> <!-- Lien vers le fichier style.css -->
</head>

<body class="align">
    <div class="grid">
        <h1 class="align">Inscription Admin</h1>

        <form action="login_admin.php" method="POST" class="form login">
            <div class="form__field">
                <label>
                    <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                </label>
                <input type="text" id="nom" class="form__input" name="nom" placeholder="Nom" required>
            </div>

            <div class="form__field">
                <label>
                    <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                </label>
                <input type="text" id="postnom" class="form__input" name="postnom" placeholder="Post-Nom" required>
            </div>

            <div class="form__field">
                <label>
                    <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                </label>
                <input type="text" id="prenom" class="form__input" name="prenom" placeholder="Prénom" required>
            </div>

            <div class="form__field">
                <label>
                    <svg class="icon"><use xlink:href="#icon-lock"></use></svg>
                </label>
                <input type="password" id="mot_de_passe" class="form__input" name="mot_de_passe" placeholder="Mot de passe" required>
            </div>

            <div class="form__field">
                <label>
                    <svg class="icon"><use xlink:href="#icon-lock"></use></svg>
                </label>
                <input type="password" id="confirmation_mot_de_passe" class="form__input" name="confirmation_mot_de_passe" placeholder="Confirmer le mot de passe" required>
            </div>
        
            <input type="submit" value="Inscription">

            <p class="text--center">Vous avez déjà un compte? <a href="login_admin.php">Connectez-vous.</a> 
                <svg class="icon">
                    <use xlink:href="#icon-arrow-right"></use>
                </svg>
            </p>
        </form>

        <div class="align">
        <?php if (isset($confirmationMessage)) { ?>
            <p><?php echo $confirmationMessage; ?></p>
        <?php } ?>
        <?php if (isset($erreur_message)) { ?>
            <p><?php echo $erreur_message; ?></p>
        <?php } ?>
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
