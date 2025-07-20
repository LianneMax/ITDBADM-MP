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
    <link rel="stylesheet" href="styles/product_modal.css">
</head>
<body>
<!-- Hero Section -->
<?php include 'includes/hero.php'; ?>

<!-- Category Filter Buttons -->
<section style="padding: 30px 60px;">
    <h2 style="font-size: 26px; margin-bottom: 20px;">Browse Products</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 10px; margin-bottom: 30px;">
        <button class="category-btn active" data-category="all">
            <i class="fas fa-box"></i> All Products
        </button>
        <?php foreach ($categories as $category): ?>
            <button class="category-btn" data-category="<?php echo htmlspecialchars($category['category_code']); ?>">
                <i class="<?php echo getCategoryIcon($category['category_name']); ?>"></i>
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
                    <div class="product-image" onclick="openProductModal('<?php echo htmlspecialchars($product['product_code']); ?>')">
                        <i class="fas fa-image" style="font-size: 24px;"></i>
                        <span style="margin-left: 8px;">Product Image</span>
                        <div class="product-image-overlay">
                            <button class="quick-view-btn" onclick="event.stopPropagation(); openProductModal('<?php echo htmlspecialchars($product['product_code']); ?>')">
                                <i class="far fa-eye"></i>
                                Quick View
                            </button>
                            <button class="product-favorite-btn" onclick="event.stopPropagation(); toggleProductFavorite('<?php echo htmlspecialchars($product['product_code']); ?>', this)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
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
                        style="font-family: 'Outfit', sans-serif; color: white;"
                        <?php echo ($product['stock_qty'] <= 0) ? 'disabled' : ''; ?>
                        data-product-code="<?php echo htmlspecialchars($product['product_code']); ?>">
                    <i class="fas fa-shopping-cart"></i> 
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

<!-- Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 style="margin: 0; color: #333;">Product Details</h2>
            <button class="modal-close" onclick="closeProductModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-image">
                <i class="fas fa-image" style="font-size: 48px; color: #ccc;"></i>
            </div>
            <div class="modal-details">
                <h1 id="modalProductName">Product Name</h1>
                <p class="modal-description" id="modalDescription">Product description goes here.</p>
                                
                <div class="modal-price" id="modalPrice">₱12,500</div>
                                
                <div class="modal-stock" id="modalStock">
                    Stock: <span class="stock-available">18 available</span>
                </div>
                
                <div class="quantity-section">
                    <label>Quantity:</label>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity()">−</button>
                        <input type="number" class="quantity-input" id="modalQuantity" value="1" min="1">
                        <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button class="add-to-cart-btn" id="modalAddToCart">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>
                    <button class="modal-favorite-btn" id="modalFavoriteBtn" onclick="toggleModalFavorite()">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentProduct = null;

// Store products data for modal
const productsData = <?php echo json_encode($products); ?>;

document.addEventListener("DOMContentLoaded", () => {
    // Category filter functionality
    const categoryButtons = document.querySelectorAll(".category-btn");
    const productCards = document.querySelectorAll(".product-card");
    const noProductsMessage = document.getElementById("noProductsMessage");
    
    categoryButtons.forEach(button => {
        button.addEventListener("click", () => {
            categoryButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");
            
            const selectedCategory = button.getAttribute("data-category");
            let visibleCount = 0;
            
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
            
            if (visibleCount === 0 && selectedCategory !== "all") {
                noProductsMessage.style.display = "block";
            } else {
                noProductsMessage.style.display = "none";
            }
        });
    });

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll(".add-to-cart-btn:not([disabled])");
    addToCartButtons.forEach(button => {
        button.addEventListener("click", () => {
            addToCart(button.getAttribute("data-product-code"), 1, button);
        });
    });

    // Modal add to cart functionality
    document.getElementById('modalAddToCart').addEventListener('click', () => {
        if (currentProduct) {
            const quantity = document.getElementById('modalQuantity').value;
            addToCart(currentProduct.product_code, quantity, document.getElementById('modalAddToCart'));
        }
    });

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        const modal = document.getElementById('productModal');
        if (event.target === modal) {
            closeProductModal();
        }
    });

    // Prevent modal from closing when clicking inside modal content
    document.querySelector('.modal-content').addEventListener('click', (event) => {
        event.stopPropagation();
    });
});

function openProductModal(productCode) {
    const product = productsData.find(p => p.product_code === productCode);
    if (!product) return;

    currentProduct = product;
    
    // Populate modal with product data
    document.getElementById('modalProductName').textContent = product.product_name;
    document.getElementById('modalDescription').textContent = product.description || 'Professional studio headphones for music production and critical listening.';
    document.getElementById('modalPrice').textContent = '₱' + Number(product.srp_php).toLocaleString();
    
    // Update stock information
    const stockElement = document.getElementById('modalStock');
    const addToCartBtn = document.getElementById('modalAddToCart');
    const quantityInput = document.getElementById('modalQuantity');
    
    if (product.stock_qty > 0) {
        stockElement.innerHTML = `Stock: <span class="stock-available">${product.stock_qty} available</span>`;
        addToCartBtn.disabled = false;
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
        quantityInput.max = product.stock_qty;
    } else {
        stockElement.innerHTML = `Stock: <span class="stock-unavailable">Out of stock</span>`;
        addToCartBtn.disabled = true;
        addToCartBtn.innerHTML = 'Out of Stock';
        quantityInput.max = 0;
    }
    
    // Reset quantity
    document.getElementById('modalQuantity').value = 1;
    
    // Show modal
    document.getElementById('productModal').style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
    currentProduct = null;
}

function increaseQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.max);
    
    if (currentValue < maxValue) {
        quantityInput.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    const currentValue = parseInt(quantityInput.value);
    
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

function toggleProductFavorite(productCode, buttonElement) {
    // Toggle the active class
    buttonElement.classList.toggle('active');
    
    // Change the icon
    const icon = buttonElement.querySelector('i');
    if (buttonElement.classList.contains('active')) {
        icon.className = 'fas fa-heart';
    } else {
        icon.className = 'far fa-heart';
    }
    
    // Here you can add AJAX call to save favorite status to database
    console.log('Toggled favorite for product:', productCode);
}

function toggleModalFavorite() {
    const favoriteBtn = document.getElementById('modalFavoriteBtn');
    favoriteBtn.classList.toggle('active');
    
    const icon = favoriteBtn.querySelector('i');
    if (favoriteBtn.classList.contains('active')) {
        icon.className = 'fas fa-heart';
    } else {
        icon.className = 'far fa-heart';
    }
    
    if (currentProduct) {
        console.log('Toggled favorite for product:', currentProduct.product_code);
    }
}

function addToCart(productCode, quantity, buttonElement) {
    const formData = new FormData();
    formData.append('product_code', productCode);
    formData.append('quantity', quantity);
    
    fetch('get_cart.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const originalText = buttonElement.textContent;
            const originalColor = buttonElement.style.backgroundColor;
            
            buttonElement.textContent = "Added!";
            buttonElement.style.backgroundColor = "#27ae60";
            
            setTimeout(() => {
                buttonElement.textContent = originalText;
                buttonElement.style.backgroundColor = originalColor || "#7f4af1";
            }, 1500);
        } else {
            alert(data.error || 'Failed to add to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add to cart');
    });
}

</script>
</body>
</html>

<?php include('includes/footer.php'); ?>

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        'Headphones' => 'fas fa-headphones',
        'Keyboards' => 'fas fa-keyboard',    
        'Mice' => 'fas fa-mouse',             
        'Monitors' => 'fas fa-desktop',       
        'Speakers' => 'fas fa-volume-up',     
        'All Products' => 'fas fa-box'               
    ];
    return isset($icons[$categoryName]) ? $icons[$categoryName] : 'fa-cogs';
}
?>