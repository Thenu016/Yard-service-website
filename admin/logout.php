<?php
session_start();         // Start session
session_unset();         // Clear all session variables
session_destroy();       // Destroy the session

// Optional: Redirect to login page
header("Location: ../login.php");
exit();
?>