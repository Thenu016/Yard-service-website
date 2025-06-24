<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$variant_id = $_POST['product_variant_id'];
$quantity = $_POST['quantity'];

// Get price of selected variant
$query = "SELECT price FROM product_variants WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $variant_id);
$stmt->execute();
$result = $stmt->get_result();
$variant = $result->fetch_assoc();

if (!$variant) {
  echo "Invalid product variant selected.";
  exit();
}

$total_price = $variant['price'] * $quantity;

// Insert into bookings
$insert = $conn->prepare("INSERT INTO bookings (user_id, product_variant_id, quantity, total_price) VALUES (?, ?, ?, ?)");
$insert->bind_param("iiid", $user_id, $variant_id, $quantity, $total_price);
$insert->execute();

echo "✅ Booking successful!<br>";
echo "<a href='booking.php'>← Book Another</a>";
?>
