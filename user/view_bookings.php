<?php

include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Handle cancel request
if (isset($_GET['cancel_id'])) {
  $cancel_id = intval($_GET['cancel_id']);
  // Cancel only if it belongs to user and is pending
  $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ? AND user_id = ? AND status = 'Pending'");
  $stmt->bind_param("ii", $cancel_id, $user_id);
  $stmt->execute();
  header("Location: view_bookings.php");
  exit();
}

// Fetch bookings
$query = "
  SELECT b.id, p.name AS product_name, pv.size_or_quantity, pv.price, b.quantity, b.status, b.booking_date
  FROM bookings b
  JOIN product_variants pv ON b.product_variant_id = pv.id
  JOIN products p ON pv.product_id = p.id
  WHERE b.user_id = ?
  ORDER BY b.booking_date DESC
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("SQL Error: " . $conn->error); // Show the actual error
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Bookings</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #f2f2f2; }
    .btn-cancel {
      padding: 6px 12px;
      background-color: red;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .btn-disabled {
      color: gray;
      pointer-events: none;
    }
  </style>
</head>
<body>

<h2>My Bookings</h2>

<table>
  <thead>
    <tr>
      <th>Product</th>
      <th>Size/Qty</th>
      <th>Unit Price</th>
      <th>Quantity</th>
      <th>Total</th>
      <th>Status</th>
      <th>Booked On</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['product_name']) ?></td>
        <td><?= htmlspecialchars($row['size_or_quantity']) ?></td>
        <td>LKR <?= number_format($row['price'], 2) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td>LKR <?= number_format($row['price'] * $row['quantity'], 2) ?></td>
        <td><?= $row['status'] ?></td>
        <td><?= date('Y-m-d H:i', strtotime($row['booking_date'])) ?></td>
        <td>
          <?php if ($row['status'] === 'Pending'): ?>
            <a href="?cancel_id=<?= $row['id'] ?>" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
          <?php else: ?>
            <span class="btn-disabled">N/A</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
