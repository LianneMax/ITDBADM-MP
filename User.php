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
    header("Location: Login.php"); 
    exit();
}

require_once 'includes/db.php'; 

$userId = $_SESSION['user_id'];

// Fetch user profile
$stmt = $conn->prepare("SELECT user_id, user_role, first_name, last_name, email, password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();

if (!$userResult) {
    // if user not found, redirect to login
    session_destroy();
    header("Location: Login.php");
    exit();
}


if (isset($_GET['error'])) {
    if ($_GET['error'] === 'unauthorized') {
        echo "<p class='error-msg'>Only customers can delete their account.</p>";
    } elseif ($_GET['error'] === 'notfound') {
        echo "<p class='error-msg'>User not found.</p>";
    }
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="styles/users.css">
  <style>
  .modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0; top: 0; 
    width: 100%; height: 100%; 
    background-color: rgba(0, 0, 0, 0.5); 
  }

  .modal-content {
    background-color: #fff;
    margin: 10% auto; 
    padding: 20px; 
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    position: relative;
  }

  .close-btn {
    position: absolute; 
    top: 10px; right: 15px; 
    font-size: 28px; 
    cursor: pointer;
  }

  .form-group {
    margin-bottom: 15px;
  }

  .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
  }

  .form-group input {
    width: 100%;
    padding: 8px;
    font-size: 1em;
  }

  .modal-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .save-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .delete-btn {
    color: #c0392b;
    text-decoration: none;
    font-weight: bold;
  }

  </style>
</head>
<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2>Edit Profile</h2>
    <form action="edit_profile.php" method="post">
      
      <div class="form-group">
        <label for="edit_first_name">First Name</label>
        <input type="text" id="edit_first_name" name="first_name" value="<?= htmlspecialchars($userResult['first_name']) ?>">
      </div>

      <div class="form-group">
        <label for="edit_last_name">Last Name</label>
        <input type="text" id="edit_last_name" name="last_name" value="<?= htmlspecialchars($userResult['last_name']) ?>">
      </div>

      <div class="modal-actions">
        <button type="submit" class="save-btn">Save Changes</button>
        <a href="delete_account.php" onclick="return confirm('Are you sure you want to delete your account?')" class="delete-btn">Delete Account</a>
      </div>
    </form>
  </div>
</div>

<body>
  <div class="container">
    <!-- Back navigation -->
    <div class="back-nav">
      <a href="index.php" class="back-btn">
        <span class="back-arrow">←</span>
        Back to Home
      </a>
    </div>
    
    <!-- User header with avatar and member info -->
    <div class="user-profile-header">
      <div class="avatar-large"><?= $userInitials ?></div>
      <div class="user-details">
        <h1><?= htmlspecialchars($fullName) ?></h1>
        <p class="member-since">Member since <?= date('n/j/Y') ?></p>
      </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="tab-navigation">
      <button class="tab-btn active" onclick="showTab('profile')">Profile</button>
      <button class="tab-btn" onclick="showTab('orders')">Order History</button>
    </div>
    
    <!-- Profile Tab Content -->
    <div id="profile" class="tab-content active">
      <div class="profile-section">
        <div class="section-header">
          <h2>
            <svg class="edit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m18 2 4 4-14 14H4v-4L18 2z"/>
            </svg>
            Profile Information
          </h2>
        </div>
        
        <div class="profile-form">
          <div class="form-row">
            <div class="form-group">
              <label for="full_name">Full Name</label>
              <div class="input-container">
                <input id="full_name" type="text" value="<?= htmlspecialchars($fullName) ?>" readonly>
                <button class="field-menu-btn">⋯</button>
              </div>
            </div>
            
            <div class="form-group">
              <label for="email">Email</label>
              <input id="email" type="email" value="<?= htmlspecialchars($userResult['email']) ?>" readonly>
            </div>
          </div>
                    
          <div class="form-actions">
            <button class="edit-profile-btn">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m18 2 4 4-14 14H4v-4L18 2z"/>
              </svg>
              Edit Profile
            </button>
            
            <button class="logout-btn" onclick="return confirmLogout()">
              <a href="?logout=1" style="text-decoration: none; color: inherit;">
                Logout
              </a>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Order History Tab Content -->
    <div id="orders" class="tab-content">
      <div class="orders-section">
        <div class="section-header">
          <h2>Order History</h2>
        </div>
        
        <?php if ($orderResults->num_rows > 0): ?>
          <div class="orders-list">
            <?php while ($order = $orderResults->fetch_assoc()): ?>
              <div class="order-card">
                <div class="order-header">
                  <div class="order-info">
                    <h3>Order #<?= htmlspecialchars($order['order_id']) ?></h3>
                    <p class="order-date"><?= date('M j, Y', strtotime($order['order_date'])) ?></p>
                  </div>
                  <span class="status-badge <?= strtolower(str_replace(' ', '-', $order['order_status'])) ?>">
                    <?= htmlspecialchars(strtoupper($order['order_status'])) ?>
                  </span>
                </div>
                
                <div class="order-amount">
                  <span class="amount">₱<?= number_format($order['totalamt_php'], 2) ?></span>
                  <?php if ($order['currency_name'] && $order['currency_name'] !== 'PHP'): ?>
                    <span class="currency-note">Original: <?= htmlspecialchars($order['currency_name']) ?></span>
                  <?php endif; ?>
                </div>
                
                <div class="order-progress">
                  <!-- Ordered Step -->
                  <div class="progress-step <?= (strtolower($order['order_status']) === 'delivered' || !empty($order['order_date'])) ? 'completed' : '' ?>">
                    <div class="step-dot"></div>
                    <div class="step-label">
                      <span>Ordered</span>
                      <small><?= date('M j', strtotime($order['order_date'])) ?></small>
                    </div>
                  </div>

                  <!-- Processing Step -->
                  <div class="progress-step <?= (strtolower($order['order_status']) === 'delivered' || in_array(strtolower($order['order_status']), ['processing', 'shipped', 'completed'])) ? 'completed' : '' ?>">
                    <div class="step-dot"></div>
                    <div class="step-label">
                      <span>Processing</span>
                    </div>
                  </div>

                  <!-- Shipped Step -->
                  <div class="progress-step <?= (strtolower($order['order_status']) === 'delivered' || in_array(strtolower($order['order_status']), ['shipped', 'completed'])) ? 'completed' : '' ?>">
                    <div class="step-dot"></div>
                    <div class="step-label">
                      <span>Shipped</span>
                    </div>
                  </div>

                  <!-- Delivered Step -->
                  <div class="progress-step <?= strtolower($order['order_status']) === 'delivered' || strtolower($order['order_status']) === 'completed' ? 'completed' : '' ?>">
                    <div class="step-dot"></div>
                    <div class="step-label">
                      <span>Delivered</span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h3>No orders found</h3>
            <p>You haven't placed any orders yet.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <script>
    function showTab(tab) {
      // Remove active class from all tab contents and buttons
      document.querySelectorAll(".tab-content").forEach(div => div.classList.remove("active"));
      document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
      
      // Add active class to selected tab content and button
      document.getElementById(tab).classList.add("active");
      event.target.classList.add("active");
    }
    
    function viewOrderDetails(orderId) {
      alert('View details for Order #' + orderId);
    }
    
    function confirmLogout() {
      return confirm('Are you sure you want to logout?');
    }

    document.querySelector(".edit-profile-btn").addEventListener("click", function () {
      document.getElementById("editProfileModal").style.display = "block";
    });

    function closeModal() {
      document.getElementById("editProfileModal").style.display = "none";
    }

    window.onclick = function (event) {
      const modal = document.getElementById("editProfileModal");
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>