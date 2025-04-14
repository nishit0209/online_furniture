<?php
// Database configuration
define('DB_HOST', 'localhost'); // Database server
define('DB_USER', 'root');      // Database username
define('DB_PASS', '');          // Database password
define('DB_NAME', 'furni'); // Database name

// Define database connection variables
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furni";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Error reporting (for development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 