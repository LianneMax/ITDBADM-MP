<?php
// Sample assigned orders data
$assignedOrders = [
    [
        'order_id' => 'ORD-001',
        'customer' => 'John Doe',
        'total' => 299.99,
        'status' => 'PROCESSING',
        'priority' => 'HIGH',
        'assigned_date' => '7/18/2024'
    ],
    [
        'order_id' => 'ORD-004',
        'customer' => 'Sarah Connor',
        'total' => 459.99,
        'status' => 'PROCESSING',
        'priority' => 'MEDIUM',
        'assigned_date' => '7/17/2024'
    ]
];

function getPriorityBadge($priority) {
    switch(strtolower($priority)) {
        case 'high':
            return '<span class="status-badge critical">HIGH</span>';
        case 'medium':
            return '<span class="status-badge low-stock">MEDIUM</span>';
        case 'low':
            return '<span class="status-badge in-stock">LOW</span>';
        default:
            return '<span class="status-badge out-of-stock">' . htmlspecialchars($priority) . '</span>';
    }
}

function getStatusBadge($status) {
    switch(strtolower($status)) {
        case 'processing':
            return '<span class="status-badge low-stock">PROCESSING</span>';
        case 'completed':
            return '<span class="status-badge in-stock">COMPLETED</span>';
        case 'pending':
            return '<span class="status-badge out-of-stock">PENDING</span>';
        case 'cancelled':
            return '<span class="status-badge critical">CANCELLED</span>';
        default:
            return '<span class="status-badge out-of-stock">' . htmlspecialchars($status) . '</span>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Assigned Orders - Staff Dashboard</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Staff Dashboard</h1>
            
            <nav class="tab-navigation">
                <a href="stock_management.php" class="tab-nav-item">Stock Management</a>
                <a href="assigned_orders.php" class="tab-nav-item active">Assigned Orders</a>
                <a href="available_orders.php" class="tab-nav-item">Available Orders</a>
            </nav>
        </div>

        <div class="card">
            <h2 class="card-title">My Assigned Orders</h2>
            
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignedOrders as $order): ?>
                    <tr>
                        <td class="product-name"><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer']); ?></td>
                        <td class="price-text">$<?php echo number_format($order['total'], 2); ?></td>
                        <td><?php echo getStatusBadge($order['status']); ?></td>
                        <td><?php echo getPriorityBadge($order['priority']); ?></td>
                        <td class="category-text"><?php echo htmlspecialchars($order['assigned_date']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view-btn" title="View Order" onclick="viewOrder('<?php echo $order['order_id']; ?>')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                    </svg>
                                </button>
                                <button class="action-btn edit-btn" title="Edit Order" onclick="editOrder('<?php echo $order['order_id']; ?>')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                </button>
                                <button class="action-btn complete-btn" title="Mark Complete" onclick="completeOrder('<?php echo $order['order_id']; ?>')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function viewOrder(orderId) {
            alert('View order: ' + orderId);
            // Add your view order logic here
        }

        function editOrder(orderId) {
            alert('Edit order: ' + orderId);
            // Add your edit order logic here
        }

        function completeOrder(orderId) {
            if (confirm('Mark order ' + orderId + ' as complete?')) {
                alert('Order ' + orderId + ' marked as complete');
                // Add your complete order logic here
            }
        }
    </script>
</body>
</html>