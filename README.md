# Rezeptverwaltung Backend (Laravel Lumen / PHP / MySQL)
Backend of an application for creation and organization of recipes as well as sharing of recipes with friends and family. 

# Local Development
- Get Copy of production database
- `$ docker-compose up`
- Import copy of production database in to local database (eg via phpmyadmin in docker container)
- Copy content of `.env.example` to `.env` file and fill out DB and JWT variables (eg `DB_HOST=127.0.0.1`, `DB_USERNAME=root`)
- `$ composer install`
- `$ php -S localhost:5500 -t rezeptverwaltung-app-backend/`
