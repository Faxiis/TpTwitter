<?php
include "vue_entete.php";

// Assure-toi que la connexion à la base de données est correctement établie
// Exemple : 
// $bdd = new PDO('mysql:host=localhost;dbname=nom_de_ta_base', 'utilisateur', 'mot_de_passe');

// Requête pour récupérer les 5 derniers utilisateurs connectés
//$requete = $bdd->query("SELECT pseudo FROM utilisateur ORDER BY derniere_connexion DESC LIMIT 5");
//$utilisateurs = $requete->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="vue/css/style.css">
</head>

<body>
    <div class="container">
        <!-- Titre de la page -->
        <h1>Voir ce qui se raconte sur…</h1>

        <!-- Formulaire de recherche -->
        <div class="search-container">
            <input type="text" id="searchText" placeholder="Saisissez votre texte..." class="search-input">
            <button id="searchButton" class="search-btn">Rechercher</button>
        </div>

        <!-- Résultat de la recherche (dynamique) -->
        <div id="result" class="result-box">
            <?php if (!empty($utilisateurs)) : ?>
                Liste des derniers membres :
                <span id="result-text">
                    <?= implode(', ', $utilisateurs); ?>
                </span>
            <?php else : 
                ?>
                Aucun membre connecté récemment.
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
