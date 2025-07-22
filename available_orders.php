<?php
<<<<<<< HEAD
session_start(); // Start the session
=======
// Sample orders data - replace with your actual data loading
$availableOrders = [
    'ORD-005' => [
        'customer' => 'Tom Hardy',
        'total' => 189.99,
        'status' => 'PENDING',
        'priority' => 'LOW',
        'date' => '7/18/2024',
        'items' => ['Gaming Headset Pro', 'Wireless Mouse'],
        'phone' => '+1 (555) 123-4567'
    ],
    'ORD-006' => [
        'customer' => 'Emma Stone',
        'total' => 299.99,
        'status' => 'PENDING',
        'priority' => 'MEDIUM',
        'date' => '7/18/2024',
        'items' => ['Mechanical Keyboard', 'Mouse Pad'],
        'phone' => '+1 (555) 987-6543'
    ],
    'ORD-007' => [
        'customer' => 'John Smith',
        'total' => 899.99,
        'status' => 'PENDING',
        'priority' => 'HIGH',
        'date' => '7/19/2024',
        'items' => ['Ultra-wide Monitor'],
        'phone' => '+1 (555) 456-7890'
    ]
];
>>>>>>> parent of e7e7f08 (users and available orders ?)

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

require_once 'includes/db.php'; // Your DB connection file

$userId = $_SESSION['user_id'];

// Handle order assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];

    if ($action === 'assign') {
        // Insert into staff_assigned_orders
        $stmt = $conn->prepare("INSERT INTO staff_assigned_orders (user_id, order_id, status) VALUES (?, ?, ?)");
        $status = 'ASSIGNED';
        $stmt->bind_param("iis", $userId, $orderId, $status);
        $stmt->execute();
    }

    header('Location: available_orders.php');
    exit();
}

<<<<<<< HEAD
// Fetch assigned order IDs
$assignedQuery = "SELECT order_id FROM staff_assigned_orders";
$assignedResult = $conn->query($assignedQuery);

$assignedOrderIds = [];
while ($row = $assignedResult->fetch_assoc()) {
    $assignedOrderIds[] = $row['order_id'];
}

// Fetch available orders (excluding assigned ones)
$ordersQuery = "SELECT * FROM orders";
$ordersResult = $conn->query($ordersQuery);

$availableOrders = [];
while ($order = $ordersResult->fetch_assoc()) {
    if (!in_array($order['order_id'], $assignedOrderIds)) {
        $availableOrders[$order['order_id']] = [
            'customer' => 'Customer Name', // Replace with actual customer data if available
            'phone' => 'Customer Phone',   // Replace with actual phone if available
            'total' => $order['totalamt_php'],
            'date' => $order['order_date'],
            'items' => [], // Add items if needed
        ];
=======
function getStatusBadge($status) {
    switch (strtoupper($status)) {
        case 'PENDING':
            return '<span class="status-badge low-stock">Pending</span>';
        case 'ASSIGNED':
            return '<span class="status-badge in-stock">Assigned</span>';
        case 'PICKED_UP':
            return '<span class="status-badge in-stock">Picked Up</span>';
        case 'CANCELLED':
            return '<span class="status-badge critical">Cancelled</span>';
        default:
            return '<span class="status-badge out-of-stock">Unknown</span>';
    }
}

function getPriorityBadge($priority) {
    switch (strtoupper($priority)) {
        case 'LOW':
            return '<span class="status-badge in-stock">Low</span>';
        case 'MEDIUM':
            return '<span class="status-badge low-stock">Medium</span>';
        case 'HIGH':
            return '<span class="status-badge critical">High</span>';
        default:
            return '<span class="status-badge out-of-stock">Normal</span>';
>>>>>>> parent of e7e7f08 (users and available orders ?)
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Orders - Staff Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .order-details {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .phone-number {
            font-weight: 500;
            color: #374151;
        }
        .action-form {
            display: inline-block;
            margin-right: 8px;
        }
        .pickup-btn {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #92400e;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .pickup-btn:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(217, 119, 6, 0.2);
        }
        .assign-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .assign-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
        }
        .view-btn {
            background-color: #f3f4f6;
            color: #6b7280;
            border: none;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .view-btn:hover {
            background-color: #dbeafe;
            color: #2563eb;
            transform: translateY(-1px);
        }
        .items-list {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Staff Dashboard</h1>
            
            <nav class="tab-navigation">
                <a href="stock_management.php" class="tab-nav-item">Stock Management</a>
                <a href="assigned_orders.php" class="tab-nav-item">Assigned Orders</a>
                <a href="available_orders.php" class="tab-nav-item active">Available Orders</a>
            </nav>
        </div>

        <div class="card">
            <div class="card-header">
                <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h2 class="card-title">Available Orders to Pick Up</h2>
            </div>
            
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($availableOrders)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6b7280; padding: 40px;">
                            No orders available for pickup at the moment.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($availableOrders as $orderId => $order): ?>
                        <tr>
                            <td class="product-name"><?php echo htmlspecialchars($orderId); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($order['customer']); ?></div>
                                <div class="order-details phone-number"><?php echo htmlspecialchars($order['phone']); ?></div>
                            </td>
                            <td class="price-text">$<?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo getStatusBadge($order['status']); ?></td>
                            <td><?php echo getPriorityBadge($order['priority']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($order['date']); ?></div>
                                <div class="items-list">
                                    <?php echo count($order['items']); ?> item(s)
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="view-btn" onclick="viewOrderDetails('<?php echo $orderId; ?>')" title="View Details">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    
                                    <form method="POST" class="action-form" onsubmit="return confirmPickup('<?php echo $orderId; ?>')">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>">
                                        <input type="hidden" name="action" value="pickup">
                                        <button type="submit" class="pickup-btn">Pick Up</button>
                                    </form>
                                    
                                    <form method="POST" class="action-form" onsubmit="return confirmAssign('<?php echo $orderId; ?>')">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>">
                                        <input type="hidden" name="action" value="assign">
                                        <button type="submit" class="assign-btn">Assign to Me</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Summary Card -->
        <?php if (!empty($availableOrders)): ?>
        <div class="card">
            <div class="card-header">
                <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h2 class="card-title">Order Summary</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                <div>
                    <h3 style="margin: 0 0 8px 0; color: #374151; font-size: 16px;">Total Orders</h3>
                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #1a202c;">
                        <?php echo count($availableOrders); ?>
                    </p>
                </div>
                
                <div>
                    <h3 style="margin: 0 0 8px 0; color: #374151; font-size: 16px;">High Priority</h3>
                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #dc2626;">
                        <?php 
                        $highPriority = array_filter($availableOrders, function($order) {
                            return strtoupper($order['priority']) === 'HIGH';
                        });
                        echo count($highPriority);
                        ?>
                    </p>
                </div>
                
                <div>
                    <h3 style="margin: 0 0 8px 0; color: #374151; font-size: 16px;">Total Value</h3>
                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #059669;">
                        $<?php 
                        $totalValue = array_sum(array_column($availableOrders, 'total'));
                        echo number_format($totalValue, 2);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function viewOrderDetails(orderId) {
            // In a real application, this would open a modal or redirect to a details page
            const orders = <?php echo json_encode($availableOrders); ?>;
            const order = orders[orderId];
            
            if (order) {
                let itemsList = order.items.join(', ');
                alert(`Order Details:\n\nOrder ID: ${orderId}\nCustomer: ${order.customer}\nPhone: ${order.phone}\nTotal: $${order.total}\nItems: ${itemsList}\nPriority: ${order.priority}\nDate: ${order.date}`);
            }
        }

        function confirmPickup(orderId) {
            return confirm(`Are you sure you want to mark order ${orderId} as picked up?\n\nThis action will remove the order from the available list.`);
        }

        function confirmAssign(orderId) {
            return confirm(`Are you sure you want to assign order ${orderId} to yourself?\n\nThis will move the order to your assigned orders list.`);
        }

        // Add hover effects for table rows
        document.querySelectorAll('.stock-table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                if (!this.querySelector('td[colspan]')) {
                    this.style.backgroundColor = '#f8fafc';
                }
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Auto-refresh page every 30 seconds to check for new orders
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>