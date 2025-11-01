Chattr API – Setup and Configuration Guide
This document provides complete setup instructions for the Chattr API, a Laravel 10 application developed with PHP 8.1.25. The API includes endpoints for posts, reactions, notifications, and supports real-time communication through WebSockets. Follow the procedures below to install, configure, and run the system locally.
1. System Requirements
•	PHP version 8.1.25 or higher
•	Composer (PHP dependency manager)
•	MySQL 5.7, 8.0, or any database supported by Laravel
•	Node.js and NPM (optional – for front-end assets)
2. Installation Procedure
Step 1 – Clone the Repository
Execute the following commands in your terminal:

git clone https://github.com/pillorajem10/chattr-api.git 
cd chattr-api

Step 2 – Install Dependencies
Run Composer to install the required PHP packages:

composer install

Step 3 – Configure the Environment File
Create the environment configuration file by copying the example:

cp .env.example .env

Note: The provided .env.example file already contains valid default values for local development. If no customization is required, you may use these defaults as-is.


Step 4 – Generate the Application Key
Run the following command to generate a unique application key:

php artisan key:generate

Step 5 – Configure Database Settings
Edit the .env file to match your database credentials. Ensure that the database exists before proceeding.

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chattr
DB_USERNAME=root
DB_PASSWORD=

Step 6 – Run Database Migrations
Execute migrations to create all required tables:

php artisan migrate

Step 7 – (Optional) Seed Sample Data
Run seeders if available to populate the database with sample records:

php artisan db:seed

3. Running the Application
Step 1 – Start the HTTP Server
Use the following command to start the Laravel development server:

php artisan serve

The default address will be: http://127.0.0.1:8000
Step 2 – Run the WebSocket Server
The Chattr API uses Laravel WebSockets for real-time updates. Run the WebSocket server in a separate terminal window alongside the main server.

php artisan websockets:serve

Ensure both processes remain active during development. The WebSocket dashboard can be accessed at http://127.0.0.1:8000/laravel-websockets.
4. WebSocket Configuration
To enable WebSockets, publish the necessary migration and configuration files and apply migrations as shown below.
Execute the following commands:

1. php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
2. php artisan migrate
3. php artisan vendor:publish --      provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"

Update the Pusher-related environment variables in the .env file as follows:

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1

5. Authentication
Chattr API uses Laravel Sanctum for token-based authentication. After a successful login or registration, a token is issued in the response. Include this token in the header for authenticated requests:
Authorization: Bearer <your_token>
6. Troubleshooting
If recent configuration changes do not appear to take effect, clear Laravel’s cached files before restarting the servers:

php artisan config:clear
php artisan cache:clear

Verify that your database credentials are correct and the database exists. Ensure both the HTTP and WebSocket servers are running concurrently.
7. Quick Command Summary
1.	composer install – Install PHP dependencies
2.	cp .env.example .env – Create environment configuration file
3.	php artisan key:generate – Generate application key
4.	php artisan migrate – Run database migrations
5.	php artisan serve – Start HTTP server
6.	php artisan websockets:serve – Start WebSocket server
8. Completion
The Chattr API should now be fully functional in your local environment. You can begin developing, testing endpoints, and verifying real-time event updates through WebSockets.
For further customization, review route definitions in routes/api.php and event broadcasting configurations in app/Events.
