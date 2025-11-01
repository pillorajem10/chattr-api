<h1 align="center">Chattr API – Setup and Configuration Guide</h1>

<p align="center">
A Laravel 10 REST API built with PHP 8.1.25, featuring posts, reactions, notifications, and real-time updates via WebSockets.
</p>

<hr/>

## Table of Contents
- [1. System Requirements](#1-system-requirements)
- [2. Installation Procedure](#2-installation-procedure)
- [3. Running the Application](#3-running-the-application)
- [4. WebSocket Configuration](#4-websocket-configuration)
- [5. Authentication](#5-authentication)
- [6. Troubleshooting](#6-troubleshooting)
- [7. Quick Command Summary](#7-quick-command-summary)
- [8. Completion](#8-completion)

<hr/>

<h2 id="1-system-requirements">1. System Requirements</h2>

Ensure your system meets the following requirements:

- PHP **8.1.25** or higher  
- Composer (PHP dependency manager)  
- MySQL **5.7/8.0** or any database supported by Laravel  
- Node.js & NPM *(optional – for frontend asset builds)*  

<hr/>

<h2 id="2-installation-procedure">2. Installation Procedure</h2>

### Step 1 – Clone the Repository
<pre><code>git clone https://github.com/pillorajem10/chattr-api.git;
cd chattr-api
</code></pre>

### Step 2 – Install Dependencies
<pre><code>composer install
</code></pre>

### Step 3 – Configure Environment File
<pre><code>cp .env.example .env
</code></pre>
<p style="color:gray"><i>Note:</i> The provided <code>.env.example</code> already contains recommended default values. You may use them directly for local setup.</p>

### Step 4 – Generate the Application Key
<pre><code>php artisan key:generate
</code></pre>

### Step 5 – Configure Database Settings
Edit <code>.env</code> and set your database credentials:

<pre><code>DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chattr
DB_USERNAME=root
DB_PASSWORD=
</code></pre>

### Step 6 – Run Database Migrations
<pre><code>php artisan migrate
</code></pre>

### Step 7 – (Optional) Seed Sample Data
<details>
<summary>Show Command</summary>

<pre><code>php artisan db:seed
</code></pre>
</details>

<hr/>

<h2 id="3-running-the-application">3. Running the Application</h2>

### Step 1 – Start the HTTP Server
<pre><code>php artisan serve
</code></pre>
Default URL: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Step 2 – Start the WebSocket Server
Open a new terminal and run:
<pre><code>php artisan websockets:serve
</code></pre>

Both servers must run simultaneously.  
The WebSocket dashboard is available at [http://127.0.0.1:8000/laravel-websockets](http://127.0.0.1:8000/laravel-websockets).

<hr/>

<h2 id="4-websocket-configuration">4. WebSocket Configuration</h2>

Publish and migrate WebSocket-related files:

<pre><code>php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
</code></pre>

Update the following environment values in <code>.env</code>:

<pre><code>BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
</code></pre>

<hr/>

<h2 id="5-authentication">5. Authentication</h2>

The Chattr API uses **Laravel Sanctum** for token-based authentication.  
Upon successful login or registration, the API returns a token.  
Include this token in the header of authenticated requests:

<pre><code>Authorization: Bearer &lt;your_token&gt;
</code></pre>

<hr/>

<h2 id="6-troubleshooting">6. Troubleshooting</h2>

If configuration changes are not taking effect, clear cached configurations:

<pre><code>php artisan config:clear
php artisan cache:clear
</code></pre>

Check the following:
- Verify `.env` database credentials.
- Ensure your database exists and is accessible.
- Keep both the HTTP and WebSocket servers running during development.

<hr/>

<h2 id="7-quick-command-summary">7. Quick Command Summary</h2>

| Command | Description |
|:--|:--|
| <code>composer install</code> | Install PHP dependencies |
| <code>cp .env.example .env</code> | Create environment file |
| <code>php artisan key:generate</code> | Generate application key |
| <code>php artisan migrate</code> | Run migrations |
| <code>php artisan serve</code> | Start HTTP server |
| <code>php artisan websockets:serve</code> | Start WebSocket server |

<hr/>

<h2 id="8-completion">8. Completion</h2>

Once setup is complete, the Chattr API should be fully functional in your local environment.  
You can now test endpoints, verify authentication, and confirm real-time updates via WebSockets.

For additional development:
- Review endpoint definitions in <code>routes/api.php</code>.  
- Inspect broadcast events in <code>app/Events</code>.  
- Access the WebSocket dashboard to monitor live event traffic.

<hr/>
<p align="center" style="color:gray">Laravel 10 • PHP 8.1.25 • MySQL • WebSockets</p>
