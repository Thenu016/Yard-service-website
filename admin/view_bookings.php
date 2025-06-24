<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// Filter logic
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

// Handle actions
if (isset($_GET['action']) && isset($_GET['booking_id'])) {
  $booking_id = intval($_GET['booking_id']);
  $action = $_GET['action'];

  if (in_array($action, ['confirmed', 'cancelled'])) {
    $update = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $update->bind_param("si", $action, $booking_id);
    $update->execute();
    header("Location: view_bookings.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - View Bookings</title>
  <style>
    body { font-family: Arial; padding: 20px; background: #f0f0f0; }
    h2 { text-align: center; }
    .filter-form { margin-bottom: 20px; text-align: center; }
    .filter-form input, .filter-form select { padding: 6px; margin: 0 5px; }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px #ccc;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }
    th { background-color: #007BFF; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .actions a {
      padding: 5px 10px;
      margin: 2px;
      border-radius: 5px;
      text-decoration: none;
      color: white;
    }
    .confirm { background-color: #28a745; }
    .cancel { background-color: #dc3545; }
    .status {
      text-transform: capitalize;
      font-weight: bold;
    }
    .status.pending { color: orange; }
    .status.confirmed { color: green; }
    .status.cancelled { color: red; }
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
<body>

  <h2>View Bookings (Admin)</h2>

  <form class="filter-form" method="GET">
    <input type="text" name="username" placeholder="Search by Username" value="<?= htmlspecialchars($filter_username) ?>">
    <select name="status">
      <option value="">All Statuses</option>
      <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="confirmed" <?= $filter_status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
      <option value="cancelled" <?= $filter_status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>
    <button type="submit">Filter</button>
  </form>

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
      <th>Action</th>
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
        <td class="status <?= $row['status'] ?>"><?= $row['status'] ?></td>
        <td class="actions">
          <?php if ($row['status'] == 'pending') { ?>
            <a href="?action=confirmed&booking_id=<?= $row['id'] ?>" class="confirm">Confirm</a>
            <a href="?action=cancelled&booking_id=<?= $row['id'] ?>" class="cancel">Cancel</a>
          <?php } else { echo "-"; } ?>
        </td>
      </tr>
    <?php } ?>
  </table>
<a href="dashboard.php" class="back-button">← Back to Dashboard</a>

</body>
</html>
