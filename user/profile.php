<?php

include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT name, username, phone, address, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .profile-box {
      border: 1px solid #ccc;
      padding: 20px;
      width: 400px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    img.profile-pic {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #444;
      margin-bottom: 10px;
    }
    .btn {
      padding: 10px 15px;
      background-color: blue;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<h2>My Profile</h2>

<div class="profile-box">
  <?php if (!empty($user['profile_pic']) && file_exists("../uploads/" . $user['profile_pic'])): ?>
    <img src="../uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="profile-pic"><br>
  <?php else: ?>
    <img src="../uploads/default.png" alt="No Profile Picture" class="profile-pic"><br>
  <?php endif; ?>

  <strong>Name:</strong> <?= htmlspecialchars($user['name']) ?><br>
  <strong>Username:</strong> <?= htmlspecialchars($user['username']) ?><br>
  <strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?><br>
  <strong>Address:</strong> <?= nl2br(htmlspecialchars($user['address'])) ?><br><br>

  <a href="edit_profile.php" class="btn">Edit Profile</a>
</div>

</body>
</html>
