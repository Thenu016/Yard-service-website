<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../login.php");
  exit();
}

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
  <style>
    body { margin: 0; font-family: Arial; display: flex; min-height: 100vh; }
    .sidebar {
      width: 220px;
      background-color: #333;
      color: white;
      padding-top: 20px;
      position: fixed;
      height: 100vh;
    }
    .sidebar a {
      display: block;
      color: white;
      padding: 15px;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #575757;
    }
    .main {
      margin-left: 220px;
      padding: 30px;
      flex: 1;
      background: #f4f4f4;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2 style="text-align:center;">Yard Service</h2>
    <a href="?page=home">🏠 Home</a>
    <a href="?page=profile">👤 Profile</a>
    <a href="?page=book_now">🛒 Book Now</a>
    <a href="?page=view_bookings">📋 View Bookings</a>
    <a href="#" onclick="confirmLogout()">Logout</a>

  </div>
  <script>
function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "logout.php"; // or the correct logout file path
  }
}
</script>



  <div class="main">
    <?php
      $allowed_pages = ['home', 'profile', 'book_now', 'view_bookings'];
      if (in_array($page, $allowed_pages) && file_exists($page . '.php')) {
        include $page . '.php';
      } else {
        echo "<h3>Page not found.</h3>";
      }
    ?>
  </div>
</body>
</html>
