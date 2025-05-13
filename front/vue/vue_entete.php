<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>RÉSEAU IIA</title>
</head>
<body>

    <navbar>
        <div class="logo-section">
            <span>RÉSEAU IIA</span>
            <img src="imgs/logoaccueil.png" alt="photo" class="photo-logo"> 
        </div>

        <div class="date-section">
            <?php echo date('d/m/Y'); ?>
        </div>

        <div class="nav-buttons">
    <?php
    if (isset($_SESSION['idUtilisateur'])) {
        echo '<a href="index.php?section=profil_Utilisateur" class="cta"><button class="btnnav"><i class="bx bxs-user-circle" id="iconUser"></i>' . $_SESSION['prenomAdh'] . ' ' . $_SESSION['nomAdh'] . '</button></a>';
        echo '<a href="index.php?section=deconnexion" class="cta"><button class="btnnav">Déconnexion</button></a>';
    } else {
        echo "<a href='index.php?section=Inscription' class='cta'><button class='btnnav'>Inscription</button></a>";
        echo "<span class='separator'>/</span>";
        echo "<a href='index.php?section=SeConnecter' class='cta'><button class='btnnav'>Connexion à mon compte</button></a>";
    }
    ?>
</div>
    </navbar>

</body>
</html>
