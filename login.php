<?php
session_start();
$conn = new mysqli("localhost", "root", "", "yard_service");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $name, $hashed, $role);

    if ($stmt->fetch() && password_verify($password, $hashed)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $name;
        $_SESSION['role'] = $role;

        if ($role === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid login credentials!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{
      background-image:url(uploads/background_image.png);
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #2c3e50;
      padding: 10px 30px;
      color: white;
    }

    .navbar .logo {
      font-size: 24px;
      font-weight: bold;
      display: flex;
      align-items: center;
    }

    .navbar .logo img {
      height: 30px;
      margin-right: 10px;
    }

    .navbar .nav-links {
      display: flex;
      gap: 20px;
    }

    .navbar .nav-links a {
      color: white;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s;
    }

    .navbar .nav-links a:hover {
      color: #f1c40f;
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
    .form-container input {
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

<div class="navbar">
    <div class="logo">
       Yard Service
    </div>
    <div class="nav-links">
      <a href="index.html">Home</a>
      <a href="aboutus.html">About Us</a>
      <a href="contactus.html">Contact Us</a>
      <a href="booking.php">Booking</a>
      <a href="login.php">Login</a>
    </div>
  </div>


<!-- Login Form -->
<div class="form-container">
  <h2>Login</h2>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
     <p>Don't have an account? <a href="register.php" style="color: lightblue;">Register here</a></p>
  </form>
  
</div>
    
</body>
</html>
