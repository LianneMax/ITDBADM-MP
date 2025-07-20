<?php
require_once 'includes/db.php';

// Get recent orders with items
$query = "
    SELECT 
        o.order_id,
        o.user_id,
        o.order_date,
        o.totalamt_php,
        o.order_status,
        oi.product_code,
        p.product_name,
        oi.quantity,
        oi.srp_php,
        oi.totalprice_php
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN products p ON oi.product_code = p.product_code
    ORDER BY o.order_id DESC
    LIMIT 50
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders Debug</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .order-group { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h2>Recent Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Item Total</th>
        </tr>
        <?php
        $current_order = null;
        while ($row = $result->fetch_assoc()):
            $is_new_order = ($current_order !== $row['order_id']);
            $current_order = $row['order_id'];
        ?>
        <tr class="<?php echo $is_new_order ? 'order-group' : ''; ?>">
            <td><?php echo $is_new_order ? $row['order_id'] : ''; ?></td>
            <td><?php echo $is_new_order ? $row['user_id'] : ''; ?></td>
            <td><?php echo $is_new_order ? $row['order_date'] : ''; ?></td>
            <td><?php echo $is_new_order ? '₱' . number_format($row['totalamt_php'], 2) : ''; ?></td>
            <td><?php echo $is_new_order ? $row['order_status'] : ''; ?></td>
            <td><?php echo $row['product_name'] ?? 'No items'; ?></td>
            <td><?php echo $row['quantity'] ?? '-'; ?></td>
            <td><?php echo $row['srp_php'] ? '₱' . number_format($row['srp_php'], 2) : '-'; ?></td>
            <td><?php echo $row['totalprice_php'] ? '₱' . number_format($row['totalprice_php'], 2) : '-'; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>