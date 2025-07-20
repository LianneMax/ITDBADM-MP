<?php
session_start();
include('includes/header.php'); 
require_once 'includes/db.php'; // Connection to DB

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user profile - Updated to match your schema
$stmt = $conn->prepare("SELECT user_id, user_role, first_name, last_name, email, password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();

if (!$userResult) {
    // User not found, redirect to login
    session_destroy();
    header("Location: Login.php");
    exit();
}

// Create full name from first_name and last_name
$fullName = trim($userResult['first_name'] . ' ' . $userResult['last_name']);

// Fetch user orders with currency information
$orderQuery = $conn->prepare("
    SELECT o.order_id, o.order_date, o.totalamt_php, o.order_status, c.currency_name, c.price_php 
    FROM orders o 
    LEFT JOIN currencies c ON o.currency_code = c.currency_code 
    WHERE o.user_id = ? 
    ORDER BY o.order_date DESC
");
$orderQuery->bind_param("i", $userId);
$orderQuery->execute();
$orderResults = $orderQuery->get_result();

// Function to get user initials for avatar
function getUserInitials($firstName, $lastName) {
    $firstInitial = !empty($firstName) ? strtoupper($firstName[0]) : '';
    $lastInitial = !empty($lastName) ? strtoupper($lastName[0]) : '';
    return $firstInitial . $lastInitial;
}

$userInitials = getUserInitials($userResult['first_name'], $userResult['last_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="styles/users.css">
  <style>
    /* Additional styles for better presentation */
    .user-role-badge {
      display: inline-block;
      background: #7f4af1;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      margin-left: 10px;
    }
    
    .currency-info {
      font-size: 14px;
      color: #666;
      margin-top: 4px;
    }
    
    .status {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }
    
    .status.processing { background: #fff3cd; color: #856404; }
    .status.completed { background: #d4edda; color: #155724; }
    .status.shipped { background: #cce7ff; color: #004085; }
    .status.cancelled { background: #f8d7da; color: #721c24; }
    
    .no-orders {
      text-align: center;
      padding: 40px;
      color: #666;
    }
    
    .order-amount {
      font-weight: 600;
      color: #2c3e50;
      margin-top: 8px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="user-header">
      <div class="avatar"><?= $userInitials ?></div>
      <div class="user-info">
        <h2>
          <?= htmlspecialchars($fullName) ?>
          <span class="user-role-badge"><?= htmlspecialchars($userResult['user_role']) ?></span>
        </h2>
        <p><?= htmlspecialchars($userResult['email']) ?></p>
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
        <div>
          <label>First Name</label>
          <input value="<?= htmlspecialchars($userResult['first_name']) ?>" readonly>
        </div>
        <div>
          <label>Last Name</label>
          <input value="<?= htmlspecialchars($userResult['last_name']) ?>" readonly>
        </div>
        <div>
          <label>Email</label>
          <input value="<?= htmlspecialchars($userResult['email']) ?>" readonly>
        </div>
        <div>
          <label>User Role</label>
          <input value="<?= htmlspecialchars($userResult['user_role']) ?>" readonly>
        </div>
        <div>
          <label>User ID</label>
          <input value="<?= htmlspecialchars($userResult['user_id']) ?>" readonly>
        </div>
      </div>
      <button class="edit-btn">✏️ Edit Profile</button>
    </div>

    <!-- Order History Section -->
    <div id="orders" class="tab-content">
      <h3>📦 Order History</h3>
      <?php if ($orderResults->num_rows > 0): ?>
        <?php while ($order = $orderResults->fetch_assoc()): ?>
          <div class="order-card">
            <div class="order-header">
              <strong>Order #<?= htmlspecialchars($order['order_id']) ?></strong> — <?= htmlspecialchars($order['order_date']) ?>
              <span class="status <?= strtolower(str_replace(' ', '', $order['order_status'])) ?>">
                <?= htmlspecialchars(strtoupper($order['order_status'])) ?>
              </span>
            </div>
            
            <div class="order-amount">
              ₱<?= number_format($order['totalamt_php'], 2) ?>
              <?php if ($order['currency_name'] && $order['currency_name'] !== 'PHP'): ?>
                <div class="currency-info">
                  Original currency: <?= htmlspecialchars($order['currency_name']) ?>
                </div>
              <?php endif; ?>
            </div>
            
            <div class="order-tracking">
              <div class="step <?= !empty($order['order_date']) ? 'done' : '' ?>">
                1. Ordered<br><?= htmlspecialchars($order['order_date']) ?>
              </div>
              <div class="step <?= in_array(strtolower($order['order_status']), ['processing', 'shipped', 'completed']) ? 'done' : '' ?>">
                2. Processing
              </div>
              <div class="step <?= in_array(strtolower($order['order_status']), ['shipped', 'completed']) ? 'done' : '' ?>">
                3. Shipped
              </div>
              <div class="step <?= strtolower($order['order_status']) === 'completed' ? 'done' : '' ?>">
                4. Delivered
              </div>
            </div>
            
            <button class="details-btn" onclick="viewOrderDetails(<?= $order['order_id'] ?>)">
              View Details
            </button>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-orders">
          <p>📦 No orders found</p>
          <p>You haven't placed any orders yet.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function showTab(tab) {
      document.querySelectorAll(".tab-content").forEach(div => div.classList.remove("active"));
      document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
      document.getElementById(tab).classList.add("active");
      event.target.classList.add("active");
    }
    
    function viewOrderDetails(orderId) {
      // You can implement this function to show order details
      // For now, just alert the order ID
      alert('View details for Order #' + orderId);
      
      // You could redirect to an order details page:
      // window.location.href = 'order_details.php?order_id=' + orderId;
      
      // Or open a modal with order details via AJAX
    }
  </script>
</body>
</html>