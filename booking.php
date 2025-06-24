<?php
session_start();
include 'db.php';

// Fetch products + variants
$query = "SELECT p.id AS product_id, p.name, p.image, pv.size_or_quantity, pv.price
          FROM products p
          JOIN product_variants pv ON p.id = pv.product_id
          ORDER BY p.name";
$result = mysqli_query($conn, $query);

// Group products
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
  $products[$row['product_id']]['name'] = $row['name'];
  $products[$row['product_id']]['image'] = $row['image'];
  $products[$row['product_id']]['variants'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Yard Service - Home</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background-image:url(uploads/background_image.png);}
    .navbar {
      background: #2c3e50; padding: 10px 30px; color: white; display: flex; justify-content: space-between;
    }
    .navbar .nav-links a { color: white; margin-left: 20px; text-decoration: none; }
    .navbar .logo { font-size: 22px; font-weight: bold; }
    .products { padding: 30px; display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
    .product-card {
      width: 300px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 15px;
    }
    .product-card img { width: 100%; height: 160px; object-fit: cover; border-radius: 6px; }
    .variant { font-size: 14px; margin-top: 5px; }
    .book-btn {
      margin-top: 10px; display: inline-block; background: #2c3e50; color: white; padding: 8px 15px;
      text-decoration: none; border-radius: 4px;
    }
    footer {
      background: #2c3e50; color: white; text-align: center; padding: 10px; margin-top: 40px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <div class="logo">Yard Service</div>
  <div class="nav-links">
    <a href="index.html">Home</a>
    <a href="aboutus.html">About Us</a>
    <a href="contactus.html">Contact Us</a>
    <a href="booking.php">Booking</a>
    <a href="login.php">Login</a>
  </div>
</div>

<!-- Booking Products Section -->
<h2 style="text-align:center; margin-top: 30px; color:white;">Available Products</h2>
<div class="products">
  <?php foreach ($products as $product): ?>
    <div class="product-card">
      <img src="admin/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <?php foreach ($product['variants'] as $variant): ?>
        <div class="variant">
          <?= htmlspecialchars($variant['size_or_quantity']) ?> – LKR <?= number_format($variant['price'], 2) ?>
        </div>
      <?php endforeach; ?>
      <a class="book-btn" href="login.php">Book Now</a>
    </div>
  <?php endforeach; ?>
</div>



</body>
</html>
