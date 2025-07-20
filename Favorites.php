<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites | TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #111;
        }

        header {
            padding: 20px;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
        }

        .header-title {
            display: flex;
            align-items: center;
            font-size: 20px;
            font-weight: 600;
            color: #111;
        }

        .header-title i {
            color: #eacb5f;
            margin-right: 10px;
        }

        .favorites-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            flex-direction: column;
            text-align: center;
        }

        .favorites-icon {
            font-size: 60px;
            color: #d6dce5;
            background-color: #eef1f6;
            padding: 30px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .favorites-text {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .favorites-subtext {
            font-size: 15px;
            color: #666;
            margin-bottom: 20px;
        }

        .browse-btn {
            padding: 10px 20px;
            background-color: #eacb5f;
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            padding: 40px;
        }

        .product-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .product-card h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0 5px;
        }

        .product-card p {
            font-size: 14px;
            color: #999;
        }
    </style>
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
$dbname = "techperipherals_db";

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
