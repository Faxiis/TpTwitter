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
    <title>Connexion à mon compte</title>
    <link rel="stylesheet" href="vue/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Connexion à mon compte</h1>

        <div class="result-box">
            <!-- Identifiant -->
            <div class="input-box">
                <label for="identifiant">Identifiant :</label>
                <input type="text" id="identifiant" name="identifiant">
            </div>
            <!-- Mot de passe et confirmation dans le même rectangle -->
            <div class="input-box">
                <label for="mdp">Mot de passe :</label>
                <input type="text" id="mdp" name="mdp">
            </div>

            <!-- Bouton de soumission -->
            <div class="submit-btn">
                <button id="submitButton">Valider</button>
            </div>
        </div>
    </div>
</body>
</html>
