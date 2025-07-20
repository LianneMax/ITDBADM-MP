<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/header.css">
</head>
<body>

<!-- Header Section -->
<header>
    <div class="logo">
        <a href="Index.php">
        <img src="assets/logo.png" alt="TechPeripherals Logo"> </a>
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
            <a href="Favorites.php"><i class="fas fa-heart"></i></a>
        </div>

        <!-- Cart -->
        <div class="icon">
            <a href="#" id="cart-toggle" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>

        <!-- Profile -->
        <div class="icon">
            <a href="User.php"><i class="fas fa-user"></i></a>
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
        loadCart();
    });

    document.getElementById('close-cart').addEventListener('click', function () {
        document.getElementById('side-cart').classList.remove('open');
    });

    function loadCart() {
        fetch('get_cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('cart-items');
            container.innerHTML = '';
            
            if (data.items.length === 0) {
                container.innerHTML = '<p class="empty-cart-msg">Your cart is empty.</p>';
            } else {
                data.items.forEach(item => {
                    container.innerHTML += `
                    <div class="cart-item" data-cart-id="${item.cart_id}">
                        <div class="item-info">
                            <strong>${item.name}</strong>
                            <p>₱${item.price} each</p>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
                        </div>
                        <div class="item-total">
                            <p>₱${item.total_price}</p>
                            <button class="remove-btn" onclick="removeItem(${item.cart_id})">×</button>
                        </div>
                    </div>
                    `;
                });
                
                // Add total at the bottom
                container.innerHTML += `
                <div class="cart-total">
                    <strong>Total: ₱${data.total}</strong>
                </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
        });
    }

    function updateQuantity(cartId, newQuantity) {
        if (newQuantity < 1) {
            removeItem(cartId);
            return;
        }
        
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('quantity', newQuantity);
        
        fetch('get_cart.php?action=update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCart(); // Reload cart
            } else {
                alert(data.error || 'Failed to update quantity');
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
        });
    }

    function removeItem(cartId) {
        if (!confirm('Remove this item from cart?')) return;
        
        const formData = new FormData();
        formData.append('cart_id', cartId);
        
        fetch('get_cart.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCart(); // Reload cart
            } else {
                alert(data.error || 'Failed to remove item');
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
        });
    }
    </script>

</body>
</html>
