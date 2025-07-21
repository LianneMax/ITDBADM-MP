<?php
session_start(); // Start the session

// Handle logout (before any output, including HTML)
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php"); // Redirect to home page
    exit();
}

// Make sure user is logged in (before any output)
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php"); // Redirect to login page if not logged in
    exit();
}

require_once 'includes/db.php'; // Connection to DB

$userId = $_SESSION['user_id'];

// Fetch user profile
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
/* Container and layout improvements */
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }

  /* Back button styles */
  .back-nav {
    margin-bottom: 20px;
  }

  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #eacb5f;
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }

  .back-btn:hover {
    background: linear-gradient(135deg, #FFFFFF 0%, #eacb5f 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
    color: white;
    text-decoration: none;
  }

  .back-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
  }

  .back-arrow {
    font-size: 16px;
    transition: transform 0.3s ease;
  }

  .back-btn:hover .back-arrow {
    transform: translateX(-2px);
  }

  /* User header layout fixes */
  .user-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
  }

  .user-header-left {
    display: flex;
    align-items: center;
    gap: 20px; /* Fixed spacing between avatar and info */
  }

  /* Avatar improvements */
  .avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: 600;
    flex-shrink: 0; /* Prevents avatar from shrinking */
  }

  /* User info spacing fixes */
  .user-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .user-info h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 12px; /* Even spacing between name and badge */
  }

  .user-info p {
    margin: 0;
    color: #6c757d;
    font-size: 16px;
  }

  /* User role badge improvements */
  .user-role-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0; /* Remove margin, use gap from parent instead */
  }

  /* Enhanced logout button */
  .logout-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    border: 2px solid transparent;
  }

  .logout-btn:hover {
    background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    border-color: rgba(255, 255, 255, 0.2);
  }

  .logout-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(255, 107, 107, 0.3);
  }

  /* Info grid improvements to prevent overlapping */
  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .info-grid > div {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .info-grid label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .info-grid input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    background: #f8f9fa;
    color: #495057;
    transition: border-color 0.3s ease;
    box-sizing: border-box; /* Prevents overflow */
  }

  .info-grid input:focus {
    outline: none;
    border-color: #667eea;
  }

  /* Tab improvements */
  .tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 30px;
    background: #f8f9fa;
    padding: 4px;
    border-radius: 12px;
  }

  .tab-btn {
    flex: 1;
    padding: 12px 24px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .tab-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .tab-btn:hover:not(.active) {
    background: rgba(255, 255, 255, 0.7);
  }

  /* Tab content */
  .tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
  }

  .tab-content.active {
    display: block;
  }

  .tab-content h3 {
    margin-bottom: 20px;
    color: #eacb5f;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Edit button improvements */
  .edit-btn {
    background: linear-gradient(135deg,rgb(237, 214, 131) 0%, #eacb5f 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
  }

  .edit-btn:hover {
    background: #eacb5f;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px #FFFFFF;
  }

  /* Order cards improvements */
  .order-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  /* Responsive improvements */
  @media (max-width: 768px) {
    .user-header {
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }
    
    .user-header-left {
      justify-content: center;
    }
    
    .info-grid {
      grid-template-columns: 1fr;
    }
    
    .tabs {
      flex-direction: column;
    }
  }

  /* Animation keyframes */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>
</head>
<body>
  <div class="container">
    <!-- Back navigation -->
    <div class="back-nav">
      <a href="index.php" class="back-btn">
        <span class="back-arrow">←</span>
        Back to Home
      </a>
    </div>
    
    <div class="user-header">
      <div class="user-header-left">
        <div class="avatar"><?= $userInitials ?></div>
        <div class="user-info">
          <h2>
            <?= htmlspecialchars($fullName) ?>
            <span class="user-role-badge"><?= htmlspecialchars($userResult['user_role']) ?></span>
          </h2>
          <p><?= htmlspecialchars($userResult['email']) ?></p>
        </div>
      </div>
      <a href="?logout=1" class="logout-btn" onclick="return confirmLogout()">🚪 Logout</a>
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
    
    function confirmLogout() {
      return confirm('Are you sure you want to logout?');
    }
  </script>
</body>
</html>