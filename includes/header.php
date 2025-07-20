<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/header.css"> <!-- Custom external CSS -->
</head>
<body>

<!-- Header Section -->
<header>
    <div class="logo">
        <img src="assets/logo.png" alt="TechPeripherals Logo">
    </div>

    <div class="search-bar">
        <input type="text" placeholder="Search products...">
        <i class="fas fa-search"></i>
    </div>

    <div class="icons">
        <!-- Currency Dropdown -->
        <div class="icon dropdown">
            <button class="currency" id="currencyDropdownButton">₱ PHP <i class="fas fa-caret-down"></i></button>
            <div class="dropdown-content" id="currencyDropdownContent">
                <div class="dropdown-item active">₱ PHP</div>
                <div class="dropdown-item">$ USD</div>
                <div class="dropdown-item">₩ KRW</div>
            </div>
        </div>

        <!-- Favorite -->
        <div class="icon">
            <a href="Favorites.php"><button><i class="fas fa-heart"></i></button></a>
        </div>

        <!-- Cart -->
        <div class="icon">
            <button id="cart-toggle" class="cart-icon">
                <i class="fas fa-shopping-cart"></i> 
            </button>
        </div>

        <!-- Profile -->
        <div class="icon">
            <a href="User.php"><button><i class="fas fa-user"></i></button></a>
        </div>
    </div>
</header>

<!-- Side Cart -->
<div id="side-cart" class="side-cart">
    <div class="side-cart-header">
        <h3>Your Cart</h3>
        <button id="close-cart">&times;</button>
    </div>
    <div id="cart-items" class="side-cart-content">
        <p class="empty-cart-msg">Your cart is empty.</p>
    </div>
    <div class="side-cart-footer">
        <button class="checkout-btn">Checkout</button>
    </div>
</div>

    <!-- Cart fetcher -->
    <script>
    document.getElementById('cart-toggle').addEventListener('click', function () {
        document.getElementById('side-cart').classList.add('open');

        // Optional: Load cart items from server
        fetch('get_cart.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('cart-items');
            container.innerHTML = '';
            if (data.length === 0) {
            container.innerHTML = '<p class="empty-cart-msg">Your cart is empty.</p>';
            } else {
            data.forEach(item => {
                container.innerHTML += `
                <div class="cart-item">
                    <p><strong>${item.name}</strong></p>
                    <p>Qty: ${item.quantity}</p>
                    <p>₱${item.price}</p>
                </div>
                `;
            });
            }
        });
    });

    document.getElementById('close-cart').addEventListener('click', function () {
        document.getElementById('side-cart').classList.remove('open');
    });
    </script>
    
</body>
</html>
