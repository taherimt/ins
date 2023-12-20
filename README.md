# Ins Project

## Description
A fullstack web application to retrieve news from news agencies 
## Requirements
- PHP 8
- MySQL / MariaDB
- Web server (e.g., Apache or Nginx)
-Laravel 10 for Backend
- React Fronend


## Backend project Steps
1. Create .env file and add your db login data and database.
2. Run `composer install` to install packages
3. Run `php artisan jwt:secret` .
4. Run  `php artisan migrate`.
5. Run  `php artisan db:seed --class=SourceTableSeeder`.
6. Run  `php artisan queue:work`.


## Frontend project Steps
1. install nodejs.
2. Run `npm install` to install packages
3. Run `npm start` to start project.



