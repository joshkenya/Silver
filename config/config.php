<?php
/**
 * Silver Entertainment - Configuration File
 * Load environment variables from .env file
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$env_file = __DIR__ . '/../.env';
if (!file_exists($env_file)) {
    die('Error: .env file not found. Please copy .env.example to .env and configure it.');
}

// Parse .env file
$env_vars = parse_ini_file($env_file);

// Define configuration constants
define('DB_HOST', $env_vars['DB_HOST'] ?? 'localhost');
define('DB_USERNAME', $env_vars['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $env_vars['DB_PASSWORD'] ?? '');
define('DB_NAME', $env_vars['DB_NAME'] ?? 'silver_entertainment');
define('DB_PORT', $env_vars['DB_PORT'] ?? 3306);

define('APP_NAME', $env_vars['APP_NAME'] ?? 'Silver Entertainment');
define('APP_ENV', $env_vars['APP_ENV'] ?? 'development');
define('APP_DEBUG', $env_vars['APP_DEBUG'] === 'true');
define('APP_URL', rtrim($env_vars['APP_URL'] ?? 'http://localhost/silver', '/'));

define('MAX_ADMINS', (int)($env_vars['MAX_ADMINS'] ?? 2));
define('MAX_FEEDBACK_IMAGES', (int)($env_vars['MAX_FEEDBACK_IMAGES'] ?? 3));
define('MAX_FILE_SIZE', (int)($env_vars['MAX_FILE_SIZE'] ?? 52428800));

define('SESSION_LIFETIME', (int)($env_vars['SESSION_LIFETIME'] ?? 120));
define('SESSION_SECURE', $env_vars['SESSION_SECURE'] === 'true');

define('NOTIFICATION_ENABLED', $env_vars['NOTIFICATION_ENABLED'] === 'true');
define('NOTIFICATION_CATEGORIES', explode(',', $env_vars['NOTIFICATION_CATEGORIES'] ?? ''));

define('AI_API_KEY', $env_vars['AI_API_KEY'] ?? '');
define('AI_MODEL', $env_vars['AI_MODEL'] ?? 'gpt-3.5-turbo');

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('ALLOWED_EXTENSIONS', explode(',', $env_vars['ALLOWED_UPLOAD_EXTENSIONS'] ?? ''));

// Timezone
date_default_timezone_set('UTC');

return array(
    'database' => array(
        'host' => DB_HOST,
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD,
        'name' => DB_NAME,
        'port' => DB_PORT,
    ),
    'app' => array(
        'name' => APP_NAME,
        'env' => APP_ENV,
        'debug' => APP_DEBUG,
        'url' => APP_URL,
    ),
    'max_admins' => MAX_ADMINS,
    'notification' => array(
        'enabled' => NOTIFICATION_ENABLED,
        'categories' => NOTIFICATION_CATEGORIES,
    ),
);
