<?php
// Inclure l'entête
include "vue_entete.php"; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PageCompte</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
          <table>
            <tr>
        <h1>Page de <?php /*echo $_SESSION['nomUtilisateur']; */?></h1> <!-- Affichage dynamique du nom d'utilisateur -->

      
                <!-- Première colonne: Titre et input -->
                <td style="text-align:left;">
                    <form action="traitement.php" method="post">
                        <!-- Ajouter une info -->
                        <div class="input-box">
                            <label for="info">Ajouter une info :</label>
                            <input type="text" id="info" name="info" placeholder="" required>
                        </div>
                        <!-- Bouton de soumission -->
                        <div class="submit-btn">
                            <button type="submit" id="submitButton">Valider</button>
                        </div>
                    </form>
                </td>

                <!-- Deuxième colonne: Photo de l'utilisateur -->
                <td class="right-section" style="text-align:center;">
                    <?php
                        $photoPath = 'path/to/user/photo.jpg'; // Remplacer par la logique de récupération de la photo

                        // Si l'utilisateur a une photo
                        if (file_exists($photoPath) && !empty($photoPath)) {
                            echo "<img src='$photoPath' alt='Photo de profil' class='profile-photo'>";
                        } else {
                            // Si pas de photo, afficher un carré bleu avec le texte "Photo"
                            echo "<div class='no-photo'>Photo</div>";
                        }
                    ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
