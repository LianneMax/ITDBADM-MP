<?php
session_start();
require_once 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
} $userId = $_SESSION['user_id'];

// Fetch Data
$products = $conn->query("SELECT * FROM products ORDER BY product_code DESC");
$staff = $conn->query("SELECT * FROM users WHERE user_role = 'staff'");
$orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
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
      <input name="name" placeholder="Product Name" required>
      <input name="category" placeholder="Category" required>
      <input name="price" type="number" step="0.01" placeholder="Price" required>
      <input name="stock" type="number" placeholder="Stock" required>
      <textarea name="description" placeholder="Product Description"></textarea>
      <button type="submit" class="yellow-btn">Add Product</button>
    </form>

    <h3>📦 Product List</h3>
    <table>
      <thead><tr><th>Name</th><th>Category</th><th>Stock</th><th>Price</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while ($p = $products->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['category']) ?></td>
          <td><?= $p['stock'] ?></td>
          <td>$<?= number_format($p['price'], 2) ?></td>
          <td>
            <a href="process/edit_product.php?id=<?= $p['id'] ?>">✏️</a>
            <a href="process/delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete product?')">🗑️</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Stock Tab -->
  <div id="stock" class="tab-content">
    <form action="process/update_stock.php" method="POST">
      <h3>🔄 Update Stock</h3>
      <select name="product_id" required>
        <option value="">Select Product</option>
        <?php
        $stockProducts = $conn->query("SELECT id, name FROM products");
        while ($sp = $stockProducts->fetch_assoc()):
        ?>
        <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
        <?php endwhile; ?>
      </select>
      <input name="quantity" type="number" placeholder="Quantity" required>
      <select name="action" required>
        <option value="">Select Action</option>
        <option value="add">Add</option>
        <option value="subtract">Subtract</option>
      </select>
      <button type="submit" class="yellow-btn">Update Stock</button>
    </form>
  </div>

  <!-- Staff Tab -->
  <div id="staff" class="tab-content">
    <form action="process/add_staff.php" method="POST" class="form-grid">
      <h3>👤 Add New Staff</h3>
      <input name="name" placeholder="Full Name" required>
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required>
      <select name="role"><option value="staff">Staff</option></select>
      <button type="submit" class="yellow-btn">Add Staff Member</button>
    </form>

    <h3>📋 Staff List</h3>
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Join Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while ($s = $staff->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($s['name']) ?></td>
          <td><?= htmlspecialchars($s['email']) ?></td>
          <td><?= $s['role'] ?></td>
          <td><?= $s['created_at'] ?></td>
          <td>
            <a href="process/delete_staff.php?id=<?= $s['id'] ?>" onclick="return confirm('Delete staff?')">🗑️</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Orders Tab -->
  <div id="orders" class="tab-content">
    <h3>📦 Order Management</h3>
    <table>
      <thead><tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while ($o = $orders->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($o['order_code']) ?></td>
          <td><?= htmlspecialchars($o['customer_name']) ?></td>
          <td>$<?= number_format($o['total'], 2) ?></td>
          <td class="status <?= strtolower($o['status']) ?>"><?= strtoupper($o['status']) ?></td>
          <td><?= $o['order_date'] ?></td>
          <td>
            <a href="orders/view.php?id=<?= $o['id'] ?>">👁️</a>
            <a href="orders/edit.php?id=<?= $o['id'] ?>">✏️</a>
            <a href="orders/delete.php?id=<?= $o['id'] ?>" onclick="return confirm('Cancel order?')">🗑️</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function showTab(tabId) {
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  event.target.classList.add('active');
}
</script>
</body>
</html>
