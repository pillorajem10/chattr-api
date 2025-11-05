# <h1 align="center">Chattr API – Setup and Configuration Guide</h1>

<p align="center">
A Laravel 10 REST API built with PHP 8.1.25, featuring posts, reactions, notifications, messages, and real-time updates via WebSockets.
</p>

<hr/>

## Table of Contents
- [1. System Requirements](#1-system-requirements)
- [2. Installation Procedure](#2-installation-procedure)
- [3. Running the Application](#3-running-the-application)
- [4. WebSocket Configuration](#4-websocket-configuration)
- [5. Authentication](#5-authentication)
- [6. Quick Command Summary](#6-quick-command-summary)
- [7. Completion](#7-completion)

<hr/>

## <h2 id="1-system-requirements">1. System Requirements</h2>

Ensure your system meets the following requirements:

- PHP **8.1.25**  
- Composer version **2.7.9**
- MySQL ***5.7/8.0*** or any database supported by Laravel  

<hr/>

## <h2 id="2-installation-procedure">2. Installation Procedure</h2>

### Step 1 – Clone the Repository
```bash
git clone https://github.com/pillorajem10/chattr-api.git
cd chattr-api
```

### Step 2 – Install Dependencies
```bash
composer install
```

### Step 3 – Configure Environment File
```bash
cp .env.example .env
```
> **Note:** The provided `.env.example` already contains recommended default values including a valid `APP_KEY`.  
> No need to run `php artisan key:generate`.

### Step 4 – Configure Database Settings
Edit your `.env` file and set your database credentials:

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chattr
DB_USERNAME=root
DB_PASSWORD=
```

### Step 5 – Run Database Migrations and Seed Data
```bash
php artisan migrate --seed
```

> **Note:** The `--seed` flag automatically populates demo data including sample users, posts, comments, messages, and notifications.  
> Default demo user password: `password`.

<hr/>

## <h2 id="3-running-the-application">3. Running the Application</h2>

### Step 1 – Start the HTTP Server
```bash
php artisan serve
```
Default URL: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Step 2 – Start the WebSocket Server
Open a new terminal and run:
```bash
php artisan websockets:serve
```

Both servers must run simultaneously.  
The WebSocket dashboard is available at [http://127.0.0.1:8000/laravel-websockets](http://127.0.0.1:8000/laravel-websockets).

<hr/>

## <h2 id="4-websocket-configuration">4. WebSocket Configuration</h2>

The Laravel WebSocket and Pusher configurations are already published and pre-configured.  
Just ensure these environment variables are present in your `.env` file:

```bash
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

No need to run `vendor:publish` commands again.

<hr/>

## <h2 id="5-authentication">5. Authentication</h2>

The Chattr API uses **Laravel Sanctum** for token-based authentication.

After running `php artisan migrate --seed`, demo users will be created with the password **"password"**.

To test authentication using **Postman**:
1. Set the method to **POST**  
2. URL: `http://127.0.0.1:8000/api/auth/login`  
3. Go to **Body → raw → JSON**  
4. Enter any seeded demo user email with password `password`

After a successful login, the API returns a token.  
Use that token in all protected endpoints by adding this header:

```
Authorization: Bearer <your_token_here>
```

<hr/>

## <h2 id="6-quick-command-summary">6. Quick Command Summary</h2>

| Command | Description |
|:--|:--|
| `composer install` | Install PHP dependencies |
| `cp .env.example .env` | Create environment file |
| `php artisan migrate --seed` | Run migrations and seed demo data |
| `php artisan serve` | Start HTTP server |
| `php artisan websockets:serve` | Start WebSocket server |

<hr/>

## <h2 id="7-completion">7. Completion</h2>

Once setup is complete, the Chattr API should be fully functional in your local environment.  
You can now test endpoints, verify authentication, and confirm real-time updates via WebSockets.

For further development:
- Review endpoint definitions in `routes/api.php`.  
- Inspect broadcast events in `app/Events`.  
- Access the WebSocket dashboard to monitor live event traffic.

<hr/>
<p align="center" style="color:gray">Laravel 10 • PHP 8.1.25 • MySQL • WebSockets</p>
