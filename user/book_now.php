<?php

include '../db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $variant_id = $_POST['product_variant_id'];
  $quantity = $_POST['quantity'];

  // Get price of selected variant
  $stmt = $conn->prepare("SELECT price FROM product_variants WHERE id = ?");
  $stmt->bind_param("i", $variant_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $variant = $result->fetch_assoc();

  if (!$variant) {
    $message = "Invalid product selected.";
  } else {
    $total_price = $variant['price'] * $quantity;

    // Insert booking
    $insert = $conn->prepare("INSERT INTO bookings (user_id, product_variant_id, quantity, total_price) VALUES (?, ?, ?, ?)");
    $insert->bind_param("iiid", $user_id, $variant_id, $quantity, $total_price);
    $insert->execute();
    $message = "✅ Booking successful!";
  }
}

// Fetch product and variant data
$query = "SELECT p.id AS product_id, p.name, p.image, pv.id AS variant_id, pv.size_or_quantity, pv.price
          FROM products p
          JOIN product_variants pv ON p.id = pv.product_id
          ORDER BY p.name";
$result = mysqli_query($conn, $query);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
  $products[$row['product_id']]['name'] = $row['name'];
  $products[$row['product_id']]['image'] = $row['image'];
  $products[$row['product_id']]['variants'][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book a Product</title>
  <style>
    body { font-family: Arial; padding: 30px; background: #f4f4f4; }
    .container { background: white; padding: 20px; max-width: 500px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    h2 { text-align: center; }
    label { font-weight: bold; display: block; margin-top: 10px; }
    select, input, button { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    img { height: 100px; margin-top: 10px; display: none; }
    .success { color: green; margin-top: 20px; text-align: center; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Book a Product</h2>

    <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

    <form method="POST">
      <label for="variant">Select Product Variant:</label>
      <select name="product_variant_id" onchange="updateDetails(this)" required>
        <option value="">-- Select Product & Size --</option>
        <?php foreach ($products as $product): ?>
          <optgroup label="<?= htmlspecialchars($product['name']) ?>">
            <?php foreach ($product['variants'] as $variant): ?>
              <option value="<?= $variant['variant_id'] ?>"
                      data-price="<?= $variant['price'] ?>"
                      data-image="../admin/uploads/<?= htmlspecialchars($variant['image'] ?? $product['image']) ?>">
                <?= $variant['size_or_quantity'] ?> - LKR <?= number_format($variant['price'], 2) ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>

      <img id="product-image" src="" alt="Product Image"><br>
      <span id="selected-price">Selected Price: -</span>

      <label for="quantity">Quantity:</label>
      <input type="number" name="quantity" min="1" required>

      <button type="submit">Book Now</button>
    </form>
  </div>

  <script>
    function updateDetails(selectElement) {
      const selected = selectElement.options[selectElement.selectedIndex];
      const price = selected.dataset.price;
      const image = selected.dataset.image;

      document.getElementById("selected-price").textContent = "Selected Price: LKR " + parseFloat(price).toFixed(2);
      
      const img = document.getElementById("product-image");
      if (image) {
        img.src = image;
        img.style.display = "block";
      } else {
        img.style.display = "none";
      }
    }
  </script>
</body>
</html>
