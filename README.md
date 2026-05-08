# 247Drank

## Requirements

Ensure you have PHP version 8.3 or higher installed.

## Installation Steps

1. **Install Composer Dependencies**
    ```bash
    composer install
    ```
2. **Copy the Configuration File**
    ```bash
    cp .env.example .env
    ```
3. **Generate Application Key**
    ```bash
    php artisan key:generate
    ```
4. **Run Migrations**
    ```bash
     php artisan migrate
    ```
    ###### or, to refresh the database and re-run all migrations:
    ```bash
    php artisan migrate:fresh
    ```
5. **Run Database Migration (migrate from old live server)**
    ```bash
     php artisan migrate-db {old_db_name}
    ```
    Expected response in [migrate_db.txt](database/migrations/migrate_db.txt)
6. **Start the Application**
    ```bash
    php artisan serve
    ```
    ## Output
    ```bash
    Laravel development server started: http://localhost:8000
    ```
