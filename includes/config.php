<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "streamit";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
session_start();

// Set timezone
date_default_timezone_set('UTC');

// Define constants
define('SITE_URL', 'http://localhost/streamit/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm']);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>