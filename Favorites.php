<?php include('includes/header.php'); ?>

<?php
// DB connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "pluggedin_itdbadm";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dummy user_id for demo (you can use sessions in real use case)
$user_id = 1;

// Fetch favorited products
$sql = "SELECT p.product_code, p.product_name, p.srp_php 
        FROM isfavorite f
        JOIN products p ON f.product_code = p.product_code
        WHERE f.user_id = ?";

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
                    <div style="height: 150px; background-color: #f0f0f0; border-radius: 8px;"></div>
                    <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p>₱<?= number_format($row['srp_php'], 2) ?></p>
                    <button style="margin-top: 10px; padding: 10px 16px; background-color: #7f4af1; color: white; border: none; border-radius: 6px; cursor: pointer;">Add to Cart</button>
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

</body>
</html>

<?php include('includes/footer.php'); ?>
