<?php
/**
 * Configuration File
 * Contains Database and Site Constants
 */

// Site Constants
define('SITE_NAME', 'Namrata Ecommerce');
define('SITE_URL', 'http://localhost/Namrata'); // Update this based on your local server URL

// Database Constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'namrata_ecommerce');

// Folder Paths
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_DIR', SITE_URL . '/uploads/products/');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
