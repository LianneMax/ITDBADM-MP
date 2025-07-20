<?php
session_start();
require_once '../includes/db.php'; // Connection to DB

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user profile
$stmt = $conn->prepare("SELECT full_name, email, phone, address, DATE(created_at) AS member_since FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();

// Fetch user orders
$orderQuery = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$orderQuery->bind_param("i", $userId);
$orderQuery->execute();
$orderResults = $orderQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="styles/user.css">
</head>
<body>
  <div class="container">
    <div class="user-header">
      <div class="avatar">JD</div>
      <div class="user-info">
        <h2><?= htmlspecialchars($userResult['full_name']) ?></h2>
        <p>Member since <?= htmlspecialchars($userResult['member_since']) ?></p>
      </div>
    </div>

    <div class="tabs">
      <button class="tab-btn active" onclick="showTab('profile')">Profile</button>
      <button class="tab-btn" onclick="showTab('orders')">Order History</button>
    </div>

    <!-- Profile Section -->
    <div id="profile" class="tab-content active">
      <h3>📑 Profile Information</h3>
      <div class="info-grid">
        <div><label>Full Name</label><input value="<?= htmlspecialchars($userResult['full_name']) ?>" readonly></div>
        <div><label>Email</label><input value="<?= htmlspecialchars($userResult['email']) ?>" readonly></div>
        <div><label>Phone</label><input value="<?= htmlspecialchars($userResult['phone']) ?>" readonly></div>
        <div><label>Address</label><input value="<?= htmlspecialchars($userResult['address']) ?>" readonly></div>
      </div>
      <button class="edit-btn">✏️ Edit Profile</button>
    </div>

    <!-- Order History Section -->
    <div id="orders" class="tab-content">
      <h3>📦 Order History</h3>
      <?php while ($order = $orderResults->fetch_assoc()): ?>
        <div class="order-card">
          <div class="order-header">
            <strong>Order <?= htmlspecialchars($order['order_code']) ?></strong> — <?= htmlspecialchars($order['order_date']) ?>
            <span class="status <?= strtolower($order['status']) ?>"><?= strtoupper($order['status']) ?></span>
          </div>
          <div class="order-tracking">
            <div class="step <?= $order['status'] !== 'ordered' ? 'done' : '' ?>">1. Ordered<br><?= $order['order_date'] ?></div>
            <div class="step <?= in_array($order['status'], ['processing', 'shipped', 'delivered']) ? 'done' : '' ?>">2. Processing</div>
            <div class="step <?= in_array($order['status'], ['shipped', 'delivered']) ? 'done' : '' ?>">3. Shipped</div>
            <div class="step <?= $order['status'] === 'delivered' ? 'done' : '' ?>">4. Delivered</div>
          </div>
          <button class="details-btn">View Details</button>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script>
    function showTab(tab) {
      document.querySelectorAll(".tab-content").forEach(div => div.classList.remove("active"));
      document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
      document.getElementById(tab).classList.add("active");
      event.target.classList.add("active");
    }
  </script>
</body>
</html>
