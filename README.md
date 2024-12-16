## Setup Steps

- git clone the repo
- run composer install  (ensure using php8.2)
- cp .env.example .env
- php artisan key:generate
- set your db credentials in the .env file
- php artisan migrate 
