<?php 
include('includes/header.php'); 
include('includes/db.php');

// Fetch all products with their categories
$sql = "SELECT p.product_code, p.product_name, p.description, p.stock_qty, p.srp_php, p.category_code, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_code = c.category_code 
        ORDER BY p.product_name";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch all categories for filter buttons
$category_sql = "SELECT DISTINCT category_code, category_name FROM categories ORDER BY category_name";
$category_result = $conn->query($category_sql);

$categories = [];
if ($category_result->num_rows > 0) {
    while($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products | TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/product_category.css">
</head>
<body>
    <!-- Hero Section -->
    <section style="text-align: center; padding: 60px 20px; background: linear-gradient(to right, #fefcea, #f1f2f6);">
        <h1 style="font-size: 42px; font-weight: 700; background: linear-gradient(to right, #7c5ca8, #eacb5f); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        Premium Tech Peripherals
        </h1>
        <p style="margin-top: 10px; font-size: 18px; color: #555;">Discover the latest in audio, input devices, and display technology. Quality gear for professionals and enthusiasts.</p>

        <div style="display: flex; justify-content: center; gap: 40px; margin-top: 40px; color: #444; font-weight: 500;">
            <div><strong><?php echo count($products); ?>+</strong><br>Premium Products</div>
            <div><strong><?php echo count($categories); ?></strong><br>Categories</div>
            <div><strong>3</strong><br>Currencies</div>
        </div>
    </section>

    <!-- Category Filter Buttons -->
    <section style="padding: 30px 60px;">
        <h2 style="font-size: 26px; margin-bottom: 20px; margin-right: 70px;">Browse Products</h2>
        <div style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 10px; margin-bottom: 30px;">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-box"></i> All Products
            </button>
            <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?php echo htmlspecialchars($category['category_code']); ?>">
                    <!-- Dynamically select icon based on category name -->
                    <i class="fas <?php echo getCategoryIcon($category['category_name']); ?>"></i> 
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Product Cards Container -->
    <section style="padding: 0 40px 60px;">
        <div id="productsContainer" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                    <h3>No products available</h3>
                    <p>Check back later for new products!</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($product['category_code']); ?>">
                        <div class="product-image">
                            <i class="fas fa-image" style="font-size: 24px;"></i>
                            <span style="margin-left: 8px;">Product Image</span>
                        </div>
                        
                        <?php if (!empty($product['category_name'])): ?>
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="product-title">
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </h3>
                        
                        <?php if (!empty($product['description'])): ?>
                            <p class="product-description">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="product-price">
                            ₱<?php echo number_format($product['srp_php'], 2); ?>
                        </div>
                        
                        <div class="product-stock <?php echo ($product['stock_qty'] <= 0) ? 'out-of-stock' : ''; ?>">
                            <?php if ($product['stock_qty'] > 0): ?>
                                <i class="fas fa-check-circle"></i> <?php echo $product['stock_qty']; ?> in stock
                            <?php else: ?>
                                <i class="fas fa-times-circle"></i> Out of stock
                            <?php endif; ?>
                        </div>
                        
                        <button class="add-to-cart-btn" 
                                <?php echo ($product['stock_qty'] <= 0) ? 'disabled' : ''; ?>
                                data-product-code="<?php echo htmlspecialchars($product['product_code']); ?>">
                            <?php echo ($product['stock_qty'] > 0) ? 'Add to Cart' : 'Out of Stock'; ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- No products message for filtered results -->
        <div id="noProductsMessage" class="no-products" style="display: none;">
            <i class="fas fa-search" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
            <h3>No products found in this category</h3>
            <p>Try selecting a different category or browse all products.</p>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Category filter functionality
            const categoryButtons = document.querySelectorAll(".category-btn");
            const productCards = document.querySelectorAll(".product-card");
            const noProductsMessage = document.getElementById("noProductsMessage");

            categoryButtons.forEach(button => {
                button.addEventListener("click", () => {
                    // Remove active class from all buttons
                    categoryButtons.forEach(btn => btn.classList.remove("active"));
                    // Add active class to clicked button
                    button.classList.add("active");
                    
                    const selectedCategory = button.getAttribute("data-category");
                    let visibleCount = 0;
                    
                    // Filter products
                    productCards.forEach(card => {
                        const productCategory = card.getAttribute("data-category");
                        
                        if (selectedCategory === "all" || productCategory === selectedCategory) {
                            card.style.display = "block";
                            card.classList.remove("hidden");
                            visibleCount++;
                        } else {
                            card.style.display = "none";
                            card.classList.add("hidden");
                        }
                    });

                    // Show/hide no products message
                    if (visibleCount === 0 && selectedCategory !== "all") {
                        noProductsMessage.style.display = "block";
                    } else {
                        noProductsMessage.style.display = "none";
                    }
                });
            });

            // Add to cart functionality (you can expand this)
            const addToCartButtons = document.querySelectorAll(".add-to-cart-btn:not([disabled])");
            addToCartButtons.forEach(button => {
                button.addEventListener("click", () => {
                    const productCode = button.getAttribute("data-product-code");
                    // Add your cart functionality here
                    alert(`Added product ${productCode} to cart!`);
                    
                    // Optional: Change button text temporarily
                    const originalText = button.textContent;
                    button.textContent = "Added!";
                    button.style.backgroundColor = "#27ae60";
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.style.backgroundColor = "#7f4af1";
                    }, 1500);
                });
            });
        });

        // Dropdown functionality (if you have currency dropdown in header)
        if (document.getElementById('currencyDropdownButton')) {
            document.getElementById('currencyDropdownButton').addEventListener('click', function() {
                const dropdownContent = document.getElementById('currencyDropdownContent');
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });

            // Add event listeners to each dropdown item
            const items = document.querySelectorAll('.dropdown-item');
            items.forEach(item => {
                item.addEventListener('click', function() {
                    items.forEach(innerItem => innerItem.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('currencyDropdownButton').innerHTML = this.innerHTML + ' <i class="fas fa-caret-down"></i>';
                    document.getElementById('currencyDropdownContent').style.display = 'none';
                });
            });

            // Close the dropdown if clicked outside
            window.addEventListener('click', function(event) {
                if (!event.target.matches('.dropdown button')) {
                    const dropdowns = document.getElementsByClassName("dropdown-content");
                    for (let i = 0; i < dropdowns.length; i++) {
                        dropdowns[i].style.display = "none";
                    }
                }
            });
        }
    </script>
</body>
</html>

<?php include('includes/footer.php'); ?>

<?php
// Function to get the appropriate Font Awesome icon class for each category
function getCategoryIcon($categoryName) {
    // Here, you can add more categories and their respective icons
    $icons = [
        'Headphones' => 'far fa-headphones',  // Outlined headphones
        'Keyboards' => 'far fa-keyboard',    // Outlined keyboard
        'Mice' => 'far fa-mouse',             // Outlined mouse
        'Monitors' => 'far fa-desktop',       // Outlined desktop
        'Speakers' => 'far fa-volume-up',     // Outlined volume up (far version)
        'All Products' => 'far fa-box'        // Outlined box       
    ];

    return isset($icons[$categoryName]) ? $icons[$categoryName] : 'fa-cogs'; // Default to fa-cogs if no icon found
}
?>
