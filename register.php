<?php
$conn = new mysqli("localhost", "root", "", "yard_service");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Handle file upload
   $profile_pic = '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['profile_pic']['tmp_name'];
        $fileName = basename($_FILES['profile_pic']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExt, $allowed)) {
            $newFileName = uniqid('user_', true) . '.' . $fileExt;
            $destination = 'uploads/' . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                $profile_pic = $newFileName;
            } else {
                $error = "Failed to upload profile picture.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.";
        }
    }


    $stmt = $conn->prepare("INSERT INTO users (name, username, phone, address, password, profile_pic) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $username, $phone, $address, $password, $profile_pic);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Register - Yard Service</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
      margin: 0;
      background-image:url(uploads/background_image.png);
    }
    
    .form-container {
      background: white;
      max-width: 400px;
      margin: 40px auto;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-container h2 {
      text-align: center;
    }
    .form-container input, .form-container textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .form-container button {
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px;
    }
    .form-container button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>



<!-- Registration Form -->
<div class="form-container">
  <h2>Register</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="phone" placeholder="Phone Number" required>
    <textarea name="address" placeholder="Address" required></textarea>
    <input type="password" name="password" placeholder="Password" required>
    <input type="file" name="profile_pic" accept="image/*" required>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="login.php" style="color: lightblue;"> Login here</a></p>
</div>

</body>
</html>
