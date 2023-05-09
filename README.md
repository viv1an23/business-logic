# System requirements
Linux OR Windows OS

Also Required Symfony installed in that system

PHP 8.2

------
# Setup : 

####
1) Run composer install
2) Create database in local and Update database credential from .env file
3) Run Following command 
     -> php bin/console doctrine:migrations:migrate
     -> php bin/console doctrine:fixtures:load
     -> symfony serve
4) Then go to url -- http://127.0.0.1:8000/login (admin@admin.com / qqqwww)
5) Run - php bin/console doctrine:migration:migrate

####

------
