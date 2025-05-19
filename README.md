# PROJET TWITTER  
Projet réalisé par Mathieu et Anthony  
URL GITHUB : https://github.com/Faxiis/TpTwitter   (la branche est la branche dev-front-back)
Ce projet est un TwitterLike, il propose une authentification, la possibilité de voir les tweets, de tweeter, de liker un tweet,  
d'accéder à son profil pour y voir ses tweets, ajouter / modifier une photo de profil...  

Ce projet est séparé en deux, d'un côté une API type REST en symfony pour le backend.  
Il est à faire tourné avec docker en suivant les instructions qui vont suivres. 
La partie front est également un projet symfony, celle-ci est à lancer à l'aide du serveur intégré symfony.  
Les instructions pour lancer le front sont également présentes plus bas.  

  
# LANCEMENT BACK  
# Ce placer dans le dossier du backend  
cd Back  
  
docker compose up -d --build  
docker compose exec php bash  

# Composer ?? Nécessaire ?
composer install  
  
# Create DB  
php bin/console doctrine:database:create  
php bin/console make:migration  
php bin/console doctrine:migrations:migrate  
  
# Load Fixtures  
php bin/console doctrine:fixtures:load --no-interaction  
  
# Utilisation  
Utiliser le back sur l'url : http://localhost:8080/  
pour PhpMyAdmin : http://localhost:8081/  
  
  
# LANCEMENT FRONT 
Pour lancer le front, on utilise le serveur embarqué symfony, se placer dans le dossier front et exécuter les commandes suivantes :  

composer install  
Symfony server:start  

Puis créez-vous un compte et découvrez !  
PS : La navigation sur l'application est longue dûe à l'utilisation de Docker et des volumes partagés.  