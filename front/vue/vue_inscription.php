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
    <title>Inscription</title>
    <link rel="stylesheet" href="vue/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>

        <div class="result-box">
            <!-- Identifiant -->
            <div class="input-box">
                <label for="identifiant">Identifiant :</label>
                <input type="text" id="identifiant" name="identifiant" placeholder="Choisissez un identifiant">
            </div>
            
            <br>

            <!-- Mot de passe et confirmation dans le même rectangle -->
            <div class="input-box password-box">
                <div class="field-group">
                    <label for="motdepasse">Mot de passe :</label>
                    <input type="password" id="motdepasse" name="motdepasse" placeholder="Mot de passe">
                </div>

                <div class="field-group">
                    <label for="confirmation">Confirmation :</label>
                    <input type="password" id="confirmation" name="confirmation" placeholder="Confirmez le mot de passe">
                </div>
            </div>

            <!-- Bouton de soumission -->
            <div class="submit-btn">
                <button id="submitButton">S'inscrire</button>
            </div>
        </div>

    </div>
</body>
</html>
