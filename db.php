<?php
$host = "localhost";      // or your database host
$username = "root";       // your database username
$password = "";           // your database password
$database = "yard_service";  // your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
