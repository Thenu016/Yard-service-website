<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// Fetch all products and their variants
$query = "SELECT p.id AS product_id, p.name, p.image, pv.size_or_quantity, pv.price
          FROM products p
          LEFT JOIN product_variants pv ON p.id = pv.product_id
          ORDER BY p.id";

$result = mysqli_query($conn, $query);
$products = [];

while ($row = mysqli_fetch_assoc($result)) {
  $products[$row['product_id']]['name'] = $row['name'];
  $products[$row['product_id']]['image'] = $row['image'];
  $products[$row['product_id']]['variants'][] = [
    'size' => $row['size_or_quantity'],
    'price' => $row['price']
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Products - Admin View</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 40px;
      color: #333;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .product {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .product:hover {
      transform: translateY(-5px);
    }

    .product img {
      max-width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 15px;
    }

    .product h3 {
      margin-bottom: 10px;
      color: #34495e;
    }

    ul {
      padding-left: 20px;
      margin-top: 5px;
      margin-bottom: 10px;
    }

    ul li {
      margin-bottom: 6px;
    }

    .edit-button {
      display: inline-block;
      margin-top: auto;
      padding: 10px 16px;
      background-color: #3498db;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 5px;
      text-align: center;
      transition: background-color 0.3s ease;
    }

    .edit-button:hover {
      background-color: #2980b9;
    }

    .back-button {
      display: block;
      width: fit-content;
      margin: 30px auto 0;
      padding: 12px 24px;
      background-color: #2ecc71;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 6px;
      text-align: center;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #27ae60;
    }

    @media (max-width: 600px) {
      .product img {
        height: 140px;
      }
    }
  </style>
</head>
<body>

  <h2>All Products</h2>

  <div class="product-grid">
    <?php foreach ($products as $product_id => $product): ?>
      <div class="product">
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <strong>Variants:</strong>
        <ul>
          <?php foreach ($product['variants'] as $v): ?>
            <li><?= htmlspecialchars($v['size']) ?> - LKR <?= number_format($v['price'], 2) ?></li>
          <?php endforeach; ?>
        </ul>
        <a href="edit_product.php?id=<?= $product_id ?>" class="edit-button">✏️ Edit Product</a>
      </div>
    <?php endforeach; ?>
  </div>

  <a href="dashboard.php" class="back-button">← Back to Dashboard</a>

</body>
</html>
