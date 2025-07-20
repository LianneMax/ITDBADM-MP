<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products | TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and general styling */
        body {
            font-family: 'Outfit', sans-serif;  /* Apply Outfit font */
            background-color: #f4f4f4;
            color: #333;
            padding: 0;
        }

        /* Navbar Styling */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Logo Section */
        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        /* Search Bar Section */
        .search-bar {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 2px 15px;
            margin: 0 20px;
            margin-left: 100px;
            width: 380px;
            background-color: #f9f9f9;
        }

        .search-bar input {
            border: none;
            width: 100%;
            padding: 8px;
            font-size: 14px;
            background-color: transparent;
            outline: none;
            border-radius: 1px;
        }

        .search-bar i {
            font-size: 18px;
            color: #aaa;
        }

        /* Icons Section */
        .icons {
            display: flex;
            align-items: center;
        }

        .icon {
            margin-left: 20px;
            cursor: pointer;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #eacb5f;
            color: white;
            font-size: 12px;
            border-radius: 50%;
            padding: 2px 6px;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #f1f1f1;
            font-size: 14px;
        }

        /* Dropdown menu styling */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .dropdown-content .dropdown-item {
            padding: 12px 16px;
            cursor: pointer;
            color: #333;
        }

        .dropdown-content .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .dropdown .active {
            background-color: #eacb5f;
            color: white;
        }

        .currency {
            padding: 8px 16px;
            background-color: #f9f9f9;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header>
        <!-- Logo Section -->
        <div class="logo">
            <img src="assets/logo.png" alt="TechPeripherals Logo">
        </div>

        <!-- Search Bar Section -->
        <div class="search-bar">
            <input type="text" placeholder="Search products...">
            <i class="fas fa-search"></i>
        </div>

        <!-- Icons Section -->
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

            <!-- Favorite Icon -->
            <div class="icon">
                <i class="fas fa-heart"></i>
            </div>

            <!-- Cart Icon (with added margin) -->
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>

            <!-- Profile Icon -->
            <div class="icon">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section style="text-align: center; padding: 60px 20px; background: linear-gradient(to right, #fefcea, #f1f2f6);">
        <h1 style="font-size: 42px; font-weight: 700; background: linear-gradient(to right, #7c5ca8, #eacb5f); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        Premium Tech Peripherals
        </h1>
        <p style="margin-top: 10px; font-size: 18px; color: #555;">Discover the latest in audio, input devices, and display technology. Quality gear for professionals and enthusiasts.</p>

        <div style="display: flex; justify-content: center; gap: 40px; margin-top: 40px; color: #444; font-weight: 500;">
            <div><strong>21+</strong><br>Premium Products</div>
            <div><strong>7</strong><br>Categories</div>
            <div><strong>3</strong><br>Currencies</div>
        </div>
    </section>

    <!-- Category Filter Buttons -->
    <section style="padding: 30px 20px;">
        <h2 style="text-align: center; font-size: 26px; margin-bottom: 20px;">Browse Products</h2>
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-bottom: 30px;">
            <button class="category-btn active">All Products</button>
            <button class="category-btn">Headphones</button>
            <button class="category-btn">Earphones</button>
            <button class="category-btn">Keyboards</button>
            <button class="category-btn">Microphones</button>
            <button class="category-btn">Monitors</button>
            <button class="category-btn">Speakers</button>
            <button class="category-btn">Mice</button>
        </div>
    </section>

    <!-- Product Cards Container -->
    <section style="padding: 0 40px 60px;">
        <div id="productsContainer" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px;">
        <!-- Product cards will be dynamically injected here -->
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 TechPeripherals. All rights reserved.</p>
    </footer>

    <!-- JavaScript for Dropdown -->
    <script>
        // Dropdown toggle functionality
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
    </script>

</body>
</html>
