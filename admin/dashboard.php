<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admin.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
    }
    .sidebar {
      width: 220px;
      background-color: #343a40;
      color: white;
      height: 100vh;
      padding-top: 20px;
      position: fixed;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: block;
      padding: 15px;
      color: white;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .main-content {
      margin-left: 220px;
      padding: 20px;
      width: calc(100% - 220px);
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="users.php">User Info</a>
  <a href="add_product.php">Add Product</a>
  <a href="view_bookings.php">View Bookings</a>
  <a href="view_products.php">View Products</a>
  
  <a href="print_report.php">Reports</a>
  <a href="#" onclick="confirmLogout()">Logout</a>
</div>
<script>
function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "logout.php"; // or the correct logout file path
  }
}
</script>


<div class="main-content">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
  <p>Select an option from the menu to begin.</p>
</div>

</body>
</html>
