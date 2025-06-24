<?php


// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Optional: fallback if username is not set
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "User";

echo "<h2>Welcome, $username!</h2>";
echo "<p>This is your Yard Service Dashboard.</p>";
?>
