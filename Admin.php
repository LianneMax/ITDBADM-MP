<?php
session_start();
require_once 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// Verify user is admin
$userCheck = $conn->prepare("SELECT user_role FROM users WHERE user_id = ?");
$userCheck->bind_param("i", $userId);
$userCheck->execute();
$userResult = $userCheck->get_result();
$user = $userResult->fetch_assoc();

if ($user['user_role'] !== 'Admin') {
    header("Location: Login.php");
    exit();
}

// Fetch Data with proper column names
$products = $conn->query("
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_code = c.category_code 
    ORDER BY p.product_code DESC
");

$staff = $conn->query("SELECT * FROM users WHERE user_role = 'Staff'");

$orders = $conn->query("
    SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
           p.payment_status, p.payment_method
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN payments p ON o.order_id = p.order_id
    ORDER BY o.order_date DESC
");

// Get categories for dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
<div class="container">
  <h2>Admin Dashboard</h2>
  <div class="user-info">Welcome, Admin | <a href="logout.php">Logout</a></div>

  <div class="tabs">
    <button class="tab-btn active" onclick="showTab('products')">Products</button>
    <button class="tab-btn" onclick="showTab('stock')">Stock</button>
    <button class="tab-btn" onclick="showTab('staff')">Staff</button>
    <button class="tab-btn" onclick="showTab('orders')">Orders</button>
  </div>

  <!-- Products Tab -->
  <div id="products" class="tab-content active">
    <form action="process/add_product.php" method="POST" class="form-grid">
      <h3>➕ Add New Product</h3>
      <input name="product_name" placeholder="Product Name" required>
      
      <select name="category_code" required>
        <option value="">Select Category</option>
        <?php 
        $categories->data_seek(0); // Reset pointer
        while ($cat = $categories->fetch_assoc()): 
        ?>
        <option value="<?= $cat['category_code'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
        <?php endwhile; ?>
      </select>
      
      <input name="srp_php" type="number" step="0.01" placeholder="Price (PHP)" required>
      <input name="stock_qty" type="number" placeholder="Stock Quantity" required>
      <textarea name="description" placeholder="Product Description" maxlength="45"></textarea>
      <button type="submit" class="yellow-btn">Add Product</button>
    </form>

    <h3>📦 Product List</h3>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Price (PHP)</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($p = $products->fetch_assoc()): ?>
          <tr>
            <td><?= $p['product_code'] ?></td>
            <td><?= htmlspecialchars($p['product_name']) ?></td>
            <td><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></td>
            <td class="<?= $p['stock_qty'] <= 10 ? 'low-stock' : '' ?>"><?= $p['stock_qty'] ?></td>
            <td>₱<?= number_format($p['srp_php'], 2) ?></td>
            <td><?= htmlspecialchars($p['description'] ?? '') ?></td>
            <td>
              <a href="process/edit_product.php?id=<?= $p['product_code'] ?>" title="Edit">✏️</a>
              <a href="process/delete_product.php?id=<?= $p['product_code'] ?>" 
                 onclick="return confirm('Delete product: <?= htmlspecialchars($p['product_name']) ?>?')" 
                 title="Delete">🗑️</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Stock Tab -->
  <div id="stock" class="tab-content">
    <form action="process/update_stock.php" method="POST">
      <h3>🔄 Update Stock</h3>
      <select name="product_code" required>
        <option value="">Select Product</option>
        <?php
        $stockProducts = $conn->query("SELECT product_code, product_name, stock_qty FROM products ORDER BY product_name");
        while ($sp = $stockProducts->fetch_assoc()):
        ?>
        <option value="<?= $sp['product_code'] ?>">
          <?= htmlspecialchars($sp['product_name']) ?> (Current: <?= $sp['stock_qty'] ?>)
        </option>
        <?php endwhile; ?>
      </select>
      <input name="new_stock" type="number" placeholder="New Stock Quantity" min="0" required>
      <button type="submit" class="yellow-btn">Update Stock</button>
    </form>

    <h3>📊 Low Stock Alert</h3>
    <div class="table-container">
      <table>
        <thead>
          <tr><th>Product</th><th>Current Stock</th><th>Category</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php
          $lowStock = $conn->query("
            SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_code = c.category_code 
            WHERE p.stock_qty <= 10 
            ORDER BY p.stock_qty ASC
          ");
          while ($ls = $lowStock->fetch_assoc()):
          ?>
          <tr class="low-stock-row">
            <td><?= htmlspecialchars($ls['product_name']) ?></td>
            <td class="low-stock"><?= $ls['stock_qty'] ?></td>
            <td><?= htmlspecialchars($ls['category_name'] ?? 'N/A') ?></td>
            <td>
              <button onclick="quickRestock(<?= $ls['product_code'] ?>, '<?= htmlspecialchars($ls['product_name']) ?>')">
                Quick Restock
              </button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Staff Tab -->
  <div id="staff" class="tab-content">
    <form action="process/add_staff.php" method="POST" class="form-grid">
      <h3>👤 Add New Staff</h3>
      <input name="first_name" placeholder="First Name" required>
      <input name="last_name" placeholder="Last Name" required>
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required minlength="6">
      <select name="user_role">
        <option value="Staff">Staff</option>
        <option value="Admin">Admin</option>
      </select>
      <button type="submit" class="yellow-btn">Add Staff Member</button>
    </form>

    <h3>📋 Staff List</h3>
    <div class="table-container">
      <table>
        <thead>
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php while ($s = $staff->fetch_assoc()): ?>
          <tr>
            <td><?= $s['user_id'] ?></td>
            <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= $s['user_role'] ?></td>
            <td>
              <a href="process/edit_staff.php?id=<?= $s['user_id'] ?>" title="Edit">✏️</a>
              <a href="process/delete_staff.php?id=<?= $s['user_id'] ?>" 
                 onclick="return confirm('Delete staff member: <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>?')"
                 title="Delete">🗑️</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Orders Tab -->
  <div id="orders" class="tab-content">
    <h3>📦 Order Management</h3>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total (PHP)</th>
            <th>Order Status</th>
            <th>Payment Status</th>
            <th>Payment Method</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($o = $orders->fetch_assoc()): ?>
          <tr>
            <td><?= $o['order_id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name'] ?? 'Unknown') ?></td>
            <td>₱<?= number_format($o['totalamt_php'], 2) ?></td>
            <td>
              <span class="status <?= strtolower($o['order_status'] ?? 'pending') ?>">
                <?= strtoupper($o['order_status'] ?? 'PENDING') ?>
              </span>
            </td>
            <td>
              <span class="payment-status <?= strtolower($o['payment_status'] ?? 'unpaid') ?>">
                <?= strtoupper($o['payment_status'] ?? 'UNPAID') ?>
              </span>
            </td>
            <td><?= strtoupper($o['payment_method'] ?? 'N/A') ?></td>
            <td><?= $o['order_date'] ?></td>
            <td>
              <a href="orders/view.php?id=<?= $o['order_id'] ?>" title="View Details">👁️</a>
              <select onchange="updateOrderStatus(<?= $o['order_id'] ?>, this.value)" class="status-select">
                <option value="">Update Status</option>
                <option value="Processing" <?= $o['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                <option value="Shipped" <?= $o['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="Delivered" <?= $o['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="Cancelled" <?= $o['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
              <a href="orders/delete.php?id=<?= $o['order_id'] ?>" 
                 onclick="return confirm('Cancel order #<?= $o['order_id'] ?>?')"
                 title="Cancel Order">🗑️</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Quick Restock Modal -->
<div id="restockModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close" onclick="closeRestockModal()">&times;</span>
    <h3>Quick Restock</h3>
    <form id="restockForm" action="process/quick_restock.php" method="POST">
      <input type="hidden" id="restock_product_code" name="product_code">
      <p>Product: <span id="restock_product_name"></span></p>
      <input type="number" id="restock_quantity" name="new_stock" placeholder="New Stock Quantity" min="1" required>
      <button type="submit" class="yellow-btn">Update Stock</button>
    </form>
  </div>
</div>

<script>
function showTab(tabId) {
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  event.target.classList.add('active');
}

function updateOrderStatus(orderId, newStatus) {
  if (newStatus && confirm(`Update order #${orderId} status to ${newStatus}?`)) {
    fetch('process/update_order_status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `order_id=${orderId}&new_status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Order status updated successfully!');
        location.reload();
      } else {
        alert('Error updating order status: ' + data.message);
      }
    })
    .catch(error => {
      alert('Error updating order status');
    });
  }
}

function quickRestock(productCode, productName) {
  document.getElementById('restock_product_code').value = productCode;
  document.getElementById('restock_product_name').textContent = productName;
  document.getElementById('restockModal').style.display = 'block';
}

function closeRestockModal() {
  document.getElementById('restockModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  const modal = document.getElementById('restockModal');
  if (event.target === modal) {
    modal.style.display = 'none';
  }
}
</script>

</body>
</html>