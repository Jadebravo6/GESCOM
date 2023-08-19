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
        
        if ($user /*&& password_verify($mot_de_passe, $user['mot_de_passe'])*/) {
            $_SESSION["matricule"] = $user['matricule']; // Stocker le matricule en session
            
            header("Location: ../index.php");
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
    <link rel="stylesheet" type="text/css" href="../style/login.css"> <!-- Lien vers le fichier style.css -->
</head>

<style>
    input[type="submit"] {
        display: flex;
        width: 100%;
        text-align: center;
        justify-content: center;
        justify-self: center;
    }
</style>

<body class="align">
    <div class="grid">
        <h1 class="align">Connexion</h1>

        <form action="" method="POST" class="form login">
            <div class="form__field">
                <label for="login__username">
                    <svg class="icon"><use xlink:href="#icon-user"></use></svg>
                </label>
                <input type="text" id="matricule" class="form__input" name="matricule" placeholder="Matricule" required>
            </div>

            <div class="form__field">
                <label for="login__password">
                    <svg class="icon"><use xlink:href="#icon-lock"></use></svg>
                    <span class="hidden">Mot de passe</span>
                </label>
                <input type="password" id="mot_de_passe" class="form__input" name="mot_de_passe" placeholder="Mot de passe" required>
            </div>

            <div class="form__field">
                <input type="submit" value="Connexion">
            </div>

            <p class="text--center">Vous n'avez pas encore de compte? <a href="inscription.php">Inscrivez-vous.</a> 
                <svg class="icon">
                    <use xlink:href="#icon-arrow-right"></use>
                </svg>
            </p>
        </form>

        <div class="align">
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
    </svg>

</body>

</html>