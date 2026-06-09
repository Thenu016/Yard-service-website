<?php
$host = getenv('DB_HOST') ?: "localhost";      // or your database host
$username = getenv('DB_USER') ?: "root";       // your database username
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ""; // your database password
$database = getenv('DB_NAME') ?: "yard_service";  // your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
