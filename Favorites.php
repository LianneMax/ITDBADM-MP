<?php include('includes/header.php'); ?>

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

<header>
    <div class="header-title"><i class="fas fa-heart"></i> My Favorites</div>
</header>

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
$sql = "SELECT p.id, p.name, p.price 
        FROM favorites f
        JOIN products p ON f.product_id = p.id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php if ($result->num_rows > 0): ?>
    <section class="product-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <div style="height: 150px; background-color: #f0f0f0; border-radius: 8px;"></div>
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p>₱<?= number_format($row['price'], 2) ?></p>
                <button style="margin-top: 10px; padding: 10px 16px; background-color: #7f4af1; color: white; border: none; border-radius: 6px; cursor: pointer;">Add to Cart</button>
            </div>
        <?php endwhile; ?>
    </section>
<?php else: ?>
    <div class="favorites-container">
        <div class="favorites-icon"><i class="fas fa-heart"></i></div>
        <div class="favorites-text">No favorites yet</div>
        <div class="favorites-subtext">Start browsing our products and click the heart icon to add items to your favorites list.</div>
        <a href="browse.php" class="browse-btn"><i class="fas fa-store"></i> Browse Products</a>
    </div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>

<?php include('includes/footer.php'); ?>