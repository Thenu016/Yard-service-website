<?php
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $sizes = $_POST['size'];
  $prices = $_POST['price'];

  $image_name = $_FILES['image']['name'];
  $image_tmp = $_FILES['image']['tmp_name'];
  $image_path = "uploads/" . basename($image_name);
  move_uploaded_file($image_tmp, $image_path);

  $stmt = $conn->prepare("INSERT INTO products (name, image) VALUES (?, ?)");
  $stmt->bind_param("ss", $name, $image_name);
  $stmt->execute();
  $product_id = $stmt->insert_id;

  $variant_stmt = $conn->prepare("INSERT INTO product_variants (product_id, size_or_quantity, price) VALUES (?, ?, ?)");
  for ($i = 0; $i < count($sizes); $i++) {
    $variant_stmt->bind_param("isd", $product_id, $sizes[$i], $prices[$i]);
    $variant_stmt->execute();
  }

  echo "<script>alert('Product added successfully!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product - Yard Service</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 40px;
      color: #333;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    form {
      max-width: 600px;
      margin: 0 auto;
      background-color: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    input[type="text"],
    input[type="number"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .variant-row {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-bottom: 10px;
    }

    .variant-row input[type="text"],
    .variant-row input[type="number"] {
      flex: 1;
    }

    .variant-row button {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .variant-row button:hover {
      background-color: #c0392b;
    }

    button[type="submit"],
    button[type="button"] {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 20px;
      margin-top: 10px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover,
    button[type="button"]:hover {
      background-color: #2980b9;
    }

    .back-button {
      display: inline-block;
      margin: 30px auto;
      padding: 10px 20px;
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

    h4 {
      color: #34495e;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

  <h2>Add New Product</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="file" name="image" required>

    <div id="variant-section">
      <h4>Variants (Size/Quantity & Price)</h4>
      <div class="variant-row">
        <input type="text" name="size[]" placeholder="Size/Quantity" required>
        <input type="number" name="price[]" step="0.01" placeholder="Price" required>
        <button type="button" onclick="removeRow(this)">Remove</button>
      </div>
    </div>

    <button type="button" onclick="addVariant()">+ Add Variant</button><br><br>
    <button type="submit">Add Product</button>
  </form>

  <div style="text-align: center;">
    <a href="dashboard.php" class="back-button">⬅ Back to Dashboard</a>
  </div>

  <script>
    function addVariant() {
      const section = document.getElementById("variant-section");
      const row = document.createElement("div");
      row.classList.add("variant-row");
      row.innerHTML = `
        <input type="text" name="size[]" placeholder="Size/Quantity" required>
        <input type="number" name="price[]" step="0.01" placeholder="Price" required>
        <button type="button" onclick="removeRow(this)">Remove</button>
      `;
      section.appendChild(row);
    }

    function removeRow(btn) {
      btn.parentElement.remove();
    }
  </script>

</body>
</html>
