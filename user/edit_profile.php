<?php
if (!isset($_SESSION)) {
  session_start();
}
include '../db.php';

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT name, username, phone, address, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];

  // Handle image upload
  $profile_pic = $user['profile_pic'];
  if (!empty($_FILES['profile_pic']['name'])) {
    $target_dir = "../uploads/";
    $filename = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . time() . '_' . $filename;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
      $profile_pic = basename($target_file);
    }
  }

  $update = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, profile_pic = ? WHERE id = ?");
  $update->bind_param("ssssi", $name, $phone, $address, $profile_pic, $user_id);
  $update->execute();

  echo "<p style='color:green;'>Profile updated successfully.</p>";

  // Refresh data
  $user['name'] = $name;
  $user['phone'] = $phone;
  $user['address'] = $address;
  $user['profile_pic'] = $profile_pic;
}
?>

<h2>My Profile</h2>

<form method="POST" enctype="multipart/form-data">
  <label>Full Name:</label><br>
  <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

  <label>Phone:</label><br>
  <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required><br><br>

  <label>Address:</label><br>
  <textarea name="address" rows="4" required><?= htmlspecialchars($user['address']) ?></textarea><br><br>

  <label>Profile Picture:</label><br>
  <?php if (!empty($user['profile_pic'])): ?>
    <img src="../uploads/<?= htmlspecialchars($user['profile_pic']) ?>" style="height:100px;"><br>
  <?php endif; ?>
  <input type="file" name="profile_pic"><br><br>

  <button type="submit">Update Profile</button>
</form>
