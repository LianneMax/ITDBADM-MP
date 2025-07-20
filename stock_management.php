<?php
// Sample stock data - replace with your actual data loading
$stockData = [
    'Gaming Headset Pro' => ['quantity' => 15, 'category' => 'Headphones', 'price' => 199.99],
    'Mechanical Keyboard' => ['quantity' => 8, 'category' => 'Keyboards', 'price' => 149.50],
    'Ultra-wide Monitor' => ['quantity' => 3, 'category' => 'Monitors', 'price' => 799.99]
];

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST['product'];
    $quantity = intval($_POST['quantity']);
    $action = $_POST['action'];
    
    if (isset($stockData[$product])) {
        if ($action === 'add') {
            $stockData[$product]['quantity'] += $quantity;
        } elseif ($action === 'remove') {
            $stockData[$product]['quantity'] = max(0, $stockData[$product]['quantity'] - $quantity);
        } elseif ($action === 'set') {
            $stockData[$product]['quantity'] = $quantity;
        }
    }
    // Save updated data here
    header('Location: stock_management.php');
    exit;
}

function getStatusBadge($quantity) {
    if ($quantity > 10) {
        return '<span class="status-badge in-stock">In Stock</span>';
    } elseif ($quantity > 5) {
        return '<span class="status-badge low-stock">Low Stock</span>';
    } elseif ($quantity > 0) {
        return '<span class="status-badge critical">Critical</span>';
    } else {
        return '<span class="status-badge out-of-stock">Out of Stock</span>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management - Staff Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Staff Dashboard</h1>
            
            <nav class="tab-navigation">
                <a href="stock_management.php" class="tab-nav-item active">Stock Management</a>
                <a href="assigned_orders.php" class="tab-nav-item">Assigned Orders</a>
                <a href="available_orders.php" class="tab-nav-item">Available Orders</a>
            </nav>
        </div>

        <div class="card">
            <div class="card-header">
                <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h2 class="card-title">Update Stock</h2>
            </div>
            
            <form method="POST" class="stock-form">
                <div class="form-group">
                    <label class="form-label">Product</label>
                    <select name="product" class="form-select" required>
                        <option value="">Select product</option>
                        <?php foreach ($stockData as $product => $data): ?>
                            <option value="<?php echo htmlspecialchars($product); ?>">
                                <?php echo htmlspecialchars($product); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-input" value="0" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select" required>
                        <option value="">Select action</option>
                        <option value="add">Add to Stock</option>
                        <option value="remove">Remove from Stock</option>
                        <option value="set">Set Stock Level</option>
                    </select>
                </div>
                
                <button type="submit" class="update-button">Update Stock</button>
            </form>
        </div>

        <div class="card">
            <h2 class="card-title">Current Stock Levels</h2>
            
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stockData as $product => $data): ?>
                    <tr>
                        <td class="product-name"><?php echo htmlspecialchars($product); ?></td>
                        <td class="category-text"><?php echo htmlspecialchars($data['category']); ?></td>
                        <td><?php echo $data['quantity']; ?></td>
                        <td class="price-text">$<?php echo number_format($data['price'], 2); ?></td>
                        <td><?php echo getStatusBadge($data['quantity']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>