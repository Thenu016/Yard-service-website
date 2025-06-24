<?php
session_start();
include '../db.php';

// Redirect to login if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// Fetch only users (excluding admins)
$query = "SELECT name, username, phone, address, profile_pic FROM users WHERE role = 'user'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Info - Admin</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
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

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #fff;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    img.profile-pic {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #007bff;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }

      .main-content {
        margin-left: 0;
        width: 100%;
      }

      table {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="users.php">User Info</a>
  <a href="add_product.php">Add Product</a>
  <a href="view_bookings.php">View Bookings</a>
  
  <a href="print_report.php">Reports</a>
  <a href="../logout.php" style="color:red;">Logout</a>
</div>

<div class="main-content">
  <h1>Registered Users</h1>

  <table>
    <thead>
      <tr>
        <th>Profile Picture</th>
        <th>Name</th>
        <th>Username</th>
        <th>Phone</th>
        <th>Address</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($user = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td>
            <?php if (!empty($user['profile_pic']) && file_exists("../uploads/" . $user['profile_pic'])): ?>
              <img src="../uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" class="profile-pic" alt="Profile">
            <?php else: ?>
              <img src="../uploads/default.png" class="profile-pic" alt="No Image">
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($user['name']); ?></td>
          <td><?php echo htmlspecialchars($user['username']); ?></td>
          <td><?php echo htmlspecialchars($user['phone']); ?></td>
          <td><?php echo htmlspecialchars($user['address']); ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

</body>
</html>
