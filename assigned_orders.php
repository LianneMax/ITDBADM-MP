<?php
session_start();
include ('includes/db.php');

// Handle status update
if ($_POST['action'] === 'update_status' && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status
    $valid_statuses = ['Processing', 'Shipped', 'Delivered'];
    if (in_array($new_status, $valid_statuses)) {
        $updateQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $new_status, $order_id);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        $updateStmt->close();
        exit;
    }
}

// Fetch assigned order IDs for the current staff member (assuming you have staff ID in session)
// For now, I'll fetch all assigned orders - you may want to filter by staff member
$assignedQuery = "SELECT order_id FROM staff_assigned_orders";
$assignedResult = $conn->query($assignedQuery);

$assignedOrderIds = [];
while ($row = $assignedResult->fetch_assoc()) {
    $assignedOrderIds[] = $row['order_id'];
}

// Fetch assigned orders with customer details
$assignedOrders = [];
if (!empty($assignedOrderIds)) {
    $orderIds = implode(',', $assignedOrderIds);
    
    $ordersQuery = "
        SELECT 
            o.order_id,
            o.user_id,
            o.order_date,
            o.totalamt_php,
            o.order_status,
            u.first_name,
            u.last_name,
            u.email
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id IN ($orderIds)
        ORDER BY o.order_date DESC
    ";
    
    $ordersResult = $conn->query($ordersQuery);
    
    while ($order = $ordersResult->fetch_assoc()) {
        // Fetch order items for this order
        $itemsQuery = "
            SELECT 
                oi.quantity,
                oi.srp_php,
                oi.totalprice_php,
                p.product_name,
                p.description
            FROM order_items oi
            JOIN products p ON oi.product_code = p.product_code
            WHERE oi.order_id = ?
        ";
        
        $itemsStmt = $conn->prepare($itemsQuery);
        $itemsStmt->bind_param("i", $order['order_id']);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();
        
        $orderItems = [];
        while ($item = $itemsResult->fetch_assoc()) {
            $orderItems[] = [
                'product_name' => $item['product_name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['srp_php'],
                'total_price' => $item['totalprice_php']
            ];
        }
        
        $assignedOrders[] = [
            'order_id' => $order['order_id'],
            'customer' => $order['first_name'] . ' ' . $order['last_name'],
            'email' => $order['email'],
            'total' => $order['totalamt_php'],
            'date' => $order['order_date'],
            'status' => $order['order_status'],
            'items' => $orderItems
        ];
        
        $itemsStmt->close();
    }
}

function getStatusBadge($status) {
    switch(strtolower($status)) {
        case 'processing':
            return '<span class="status-badge low-stock">PROCESSING</span>';
        case 'shipped':
            return '<span class="status-badge in-stock">SHIPPED</span>';
        case 'delivered':
            return '<span class="status-badge in-stock">DELIVERED</span>';
        case 'pending':
            return '<span class="status-badge out-of-stock">PENDING</span>';
        case 'cancelled':
            return '<span class="status-badge critical">CANCELLED</span>';
        default:
            return '<span class="status-badge out-of-stock">' . htmlspecialchars($status) . '</span>';
    }
}

function getStatusDropdown($currentStatus, $orderId) {
    $statuses = ['Processing', 'Shipped', 'Delivered'];
    $dropdown = '<select class="status-dropdown" onchange="updateOrderStatus(' . $orderId . ', this.value)">';
    
    foreach ($statuses as $status) {
        $selected = (strtolower($currentStatus) === strtolower($status)) ? 'selected' : '';
        $dropdown .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
    }
    
    $dropdown .= '</select>';
    return $dropdown;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assigned Orders - Staff Dashboard</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .status-dropdown {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }
    </style>
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
            
            <?php if (empty($assignedOrders)): ?>
                <p>No orders assigned to you at the moment.</p>
            <?php else: ?>
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignedOrders as $order): ?>
                    <tr>
                        <td class="product-name"><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer']); ?></td>
                        <td class="price-text">₱<?php echo number_format($order['total'], 2); ?></td>
                        <td><?php echo getStatusDropdown($order['status'], $order['order_id']); ?></td>
                        <td class="category-text"><?php echo htmlspecialchars($order['date']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view-btn" title="View Order" onclick="viewOrder('<?php echo $order['order_id']; ?>')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Store order data for JavaScript access
        const orderData = <?php echo json_encode(array_column($assignedOrders, null, 'order_id')); ?>;

        function viewOrder(orderId) {
            const order = orderData[orderId];
            if (order) {
                // Format items list from the array of objects
                let itemsList = order.items.map(item => 
                    `${item.product_name} (Qty: ${item.quantity}, ₱${item.unit_price} each, Total: ₱${item.total_price})`
                ).join('\n');
                
                alert(`Order Details:\n\nOrder ID: ${orderId}\nCustomer: ${order.customer}\nEmail: ${order.email}\nStatus: ${order.status}\nTotal: ₱${order.total}\nDate: ${order.date}\n\nItems:\n${itemsList}`);
            }
        }

        function updateOrderStatus(orderId, newStatus) {
            if (confirm(`Update order ${orderId} status to ${newStatus}?`)) {
                // Create form data
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('order_id', orderId);
                formData.append('new_status', newStatus);

                // Send AJAX request
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order status updated successfully!');
                        location.reload(); // Refresh the page to show updated data
                    } else {
                        alert('Failed to update order status: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error updating order status: ' + error);
                });
            }
        }
    </script>
</body>
</html>