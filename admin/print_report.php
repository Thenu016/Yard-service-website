<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

$filter_username = $_GET['username'] ?? '';
$filter_status = $_GET['status'] ?? '';

$sql = "SELECT b.id, u.username, p.name AS product_name, pv.size_or_quantity, b.quantity, b.total_price, b.booking_date, b.status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN product_variants pv ON b.product_variant_id = pv.id
        JOIN products p ON pv.product_id = p.id
        WHERE 1";

if (!empty($filter_username)) {
  $sql .= " AND u.username LIKE '%" . mysqli_real_escape_string($conn, $filter_username) . "%'";
}
if (!empty($filter_status)) {
  $sql .= " AND b.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}
$sql .= " ORDER BY b.booking_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Printable Booking Report</title>
  <style>
    body { font-family: Arial; padding: 20px; color: #000; }
    h2 { text-align: center; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      border: 1px solid #444;
      padding: 8px;
      text-align: center;
    }
    th {
      background-color: #eee;
    }
    .status {
      text-transform: capitalize;
    }
    @media print {
      .no-print {
        display: none;
      }
    }

    .back-button {
  display: inline-block;
  margin: 10px 0;
  padding: 8px 16px;
  background-color: #4CAF50;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.back-button:hover {
  background-color: #45a049;
}

  </style>
</head>
<body onload="window.print()">

  <div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()">🖨️ Print</button>
    <a href="view_bookings.php">🔙 Back to View Bookings</a>
  </div>

  <h2>Booking Report</h2>
  <p><strong>Username Filter:</strong> <?= htmlspecialchars($filter_username ?: 'All') ?> |
     <strong>Status:</strong> <?= htmlspecialchars($filter_status ?: 'All') ?></p>

  <table>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Product</th>
      <th>Size / Qty</th>
      <th>Booked Qty</th>
      <th>Total (LKR)</th>
      <th>Booked At</th>
      <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['product_name']) ?></td>
        <td><?= htmlspecialchars($row['size_or_quantity']) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= number_format($row['total_price'], 2) ?></td>
        <td><?= $row['booking_date'] ?></td>
        <td class="status"><?= $row['status'] ?></td>
      </tr>
    <?php } ?>
  </table>
  <a href="dashboard.php" class="back-button">← Back to Dashboard</a>

</body>
</html>