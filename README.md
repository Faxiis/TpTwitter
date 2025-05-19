# LANCEMENT BACK
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
composer install  
Symfony server:start  