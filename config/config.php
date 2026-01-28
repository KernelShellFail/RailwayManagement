<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'railway_system');
define('DB_USER', 'root');
define('DB_PASS', 'basit');

// Application configuration
define('APP_NAME', 'Railway Reservation System');
define('APP_URL', 'http://localhost/railway-system');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Security
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_NAME', 'railway_session');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start secure session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_name(SESSION_NAME);
session_start();
?>