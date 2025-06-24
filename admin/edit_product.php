<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Invalid product ID.";
  exit();
}

$product_id = intval($_GET['id']);
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];

  // Update image if new one uploaded
  if (!empty($_FILES['image']['name'])) {
    $image_name = basename($_FILES['image']['name']);
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = "uploads/" . $image_name;
    move_uploaded_file($image_tmp, $image_path);

    $stmt = $conn->prepare("UPDATE products SET name = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $image_name, $product_id);
  } else {
    $stmt = $conn->prepare("UPDATE products SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $product_id);
  }
  $stmt->execute();

  // Update existing variants
  if (!empty($_POST['variant_id'])) {
    foreach ($_POST['variant_id'] as $index => $vid) {
      $size = $_POST['size'][$index];
      $price = $_POST['price'][$index];
      $update_stmt = $conn->prepare("UPDATE product_variants SET size_or_quantity = ?, price = ? WHERE id = ?");
      $update_stmt->bind_param("sdi", $size, $price, $vid);
      $update_stmt->execute();
    }
  }

  // Insert new variants
  if (!empty($_POST['new_size'])) {
    foreach ($_POST['new_size'] as $index => $size) {
      if (!empty($size)) {
        $price = $_POST['new_price'][$index];
        $insert_stmt = $conn->prepare("INSERT INTO product_variants (product_id, size_or_quantity, price) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("isd", $product_id, $size, $price);
        $insert_stmt->execute();
      }
    }
  }

  $message = "✅ Product updated successfully!";
}

// Get product info
$pstmt = $conn->prepare("SELECT name, image FROM products WHERE id = ?");
$pstmt->bind_param("i", $product_id);
$pstmt->execute();
$product_result = $pstmt->get_result();
$product = $product_result->fetch_assoc();

$variant_result = mysqli_query($conn, "SELECT id, size_or_quantity, price FROM product_variants WHERE product_id = $product_id");
$variants = mysqli_fetch_all($variant_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      padding: 30px;
    }

    .container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }

    input[type="text"], input[type="file"], input[type="number"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .variant-row {
      display: flex;
      gap: 10px;
      margin-bottom: 10px;
    }

    .variant-row input {
      flex: 1;
    }

    .btn {
      padding: 10px 20px;
      background-color: #27ae60;
      color: white;
      border: none;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
    }

    .btn:hover {
      background-color: #219150;
    }

    .message {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .back {
      text-align: center;
      margin-top: 20px;
    }

    .back a {
      color: #3498db;
      text-decoration: none;
    }

    img {
      height: 120px;
      margin-top: 10px;
      border-radius: 8px;
      display: block;
    }
    img.fullsize {
    max-width: none !important;
    width: auto !important;
    height: auto !important;
    display: block;
}

    .section-title {
      margin-top: 30px;
      font-size: 18px;
      color: #444;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Edit Product</h2>
  <?php if ($message): ?><p class="message"><?= $message ?></p><?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Product Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Product Image:</label>
    <input type="file" name="image">
    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">

    <div class="section-title">Existing Variants</div>
    <?php foreach ($variants as $v): ?>
      <div class="variant-row">
        <input type="hidden" name="variant_id[]" value="<?= $v['id'] ?>">
        <input type="text" name="size[]" value="<?= htmlspecialchars($v['size_or_quantity']) ?>" required placeholder="Size/Qty">
        <input type="number" name="price[]" value="<?= htmlspecialchars($v['price']) ?>" step="0.01" required placeholder="Price">
      </div>
    <?php endforeach; ?>

    <div class="section-title">Add New Variants</div>
    <div id="new-variants">
      <div class="variant-row">
        <input type="text" name="new_size[]" placeholder="Size/Qty">
        <input type="number" name="new_price[]" step="0.01" placeholder="Price">
      </div>
    </div>
    <button type="button" class="btn" onclick="addVariant()">+ Add More</button>

    <br><button type="submit" class="btn">Save Changes</button>
  </form>

  <div class="back">
    <a href="view_products.php">← Back to All Products</a>
  </div>
</div>

<script>
  function addVariant() {
    const container = document.getElementById("new-variants");
    const row = document.createElement("div");
    row.classList.add("variant-row");
    row.innerHTML = `
      <input type="text" name="new_size[]" placeholder="Size/Qty">
      <input type="number" name="new_price[]" step="0.01" placeholder="Price">
    `;
    container.appendChild(row);
  }
</script>

</body>
</html>
