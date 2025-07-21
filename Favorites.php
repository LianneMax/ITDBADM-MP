<?php
// DB connection
include('includes/db.php');

// Dummy user_id for demo (you can use sessions in real use case)
$user_id = 1;

// Fetch favorited products with more details
$sql = "SELECT p.product_code, p.product_name, p.description, p.srp_php, p.stock_qty, c.category_name 
        FROM isfavorite f
        JOIN products p ON f.product_code = p.product_code
        LEFT JOIN categories c ON p.category_code = c.category_code
        WHERE f.user_id = ?
        ORDER BY p.product_name";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites | TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/favorites.css">
</head>
<body>

<!-- Page Title Section -->
<div class="page-title">
    <div class="container">
        <h1><i class="far fa-heart"></i> My Favorites</h1>
    </div>
</div>

<?php if ($result->num_rows > 0): ?>
    <section class="product-grid">
        <div class="container">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div style="height: 150px; background-color: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;">
                        <i class="fas fa-image" style="font-size: 24px;"></i>
                    </div>
                    <?php if (!empty($row['category_name'])): ?>
                        <div style="font-size: 12px; color: #666; margin-top: 8px;"><?= htmlspecialchars($row['category_name']) ?></div>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p style="font-size: 14px; color: #666; margin: 4px 0;"><?= htmlspecialchars($row['description'] ?? '') ?></p>
                    <p style="font-size: 18px; font-weight: 600; color: #7f4af1;">₱<?= number_format($row['srp_php'], 2) ?></p>
                    <div style="display: flex; gap: 8px; margin-top: 10px;">
                        <button style="flex: 1; padding: 10px 16px; background-color: #7f4af1; color: white; border: none; border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button onclick="removeFromFavorites('<?= $row['product_code'] ?>')" 
                                style="padding: 10px 12px; background-color: #ff4757; color: white; border: none; border-radius: 6px; cursor: pointer;" 
                                title="Remove from favorites">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
<?php else: ?>
    <div class="favorites-container">
        <div class="favorites-icon"><i class="fas fa-heart"></i></div>
        <div class="favorites-text">No favorites yet</div>
        <div class="favorites-subtext">Start browsing our products and click the heart icon to add items to your favorites list.</div>
        <a href="index.php" class="browse-btn"><i class="fas fa-shopping-bag"></i> Browse Products</a>
    </div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

<script>
function removeFromFavorites(productCode) {
    const formData = new FormData();
    formData.append('product_code', productCode);
    
    fetch('toggle_favorite.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.action === 'removed') {
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            console.error('Error removing from favorites:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>
