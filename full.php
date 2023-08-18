<!DOCTYPE html>
<html>
<head>
    <title>Publication complète</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Lien vers le fichier style.css -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .pdf-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        .pdf-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .pdf-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .pdf-date {
            font-size: 14px;
            color: #666;
        }

        .pdf-content {
            font-size: 16px;
            line-height: 1.6;
            text-align: justify;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "projet_ecole";

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $publicationId = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM publication WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $publicationId);
        $stmt->execute();
        $publication = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
    ?>

    <div class="pdf-container">
        <div class="pdf-header">
            <div class="pdf-title"><?php echo $publication['titre_publication']; ?></div>
            <div class="pdf-date">Publié le <?php echo $publication['date_publication']; ?></div>
        </div>
        <div class="pdf-content">
            <?php echo $publication['contenu_publication']; ?>
        </div>
    </div>

    <script>
        const modal = document.querySelector('.pdf-container');
        modal.style.display = 'block';
    </script>
</body>
</html>
