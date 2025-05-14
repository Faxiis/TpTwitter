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
    <title>Mon compte</title>
    <link rel="stylesheet" href="vue/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Mon compte</h1>

        <div class="result-box">
            <!-- Formulaire pour télécharger une image -->
            <form action="modifCompte.php" method="post" enctype="multipart/form-data">
                <!-- Ajouter une photo -->
                <div class="input-box">
                    <label for="imageInput">Ajouter une photo :</label>
                    <!-- Champ de fichier pour télécharger l'image -->
                    <input type="file" id="imageInput" name="imageInput" accept="image/*">
                </div>

                <!-- Bouton de soumission -->
                <div class="submit-btn">
                    <button type="submit" id="submitButton">Valider</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
