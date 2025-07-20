<?php include('includes/header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products | TechPeripherals</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Product category styles */
        .category-btn {
            padding: 10px 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .category-btn:hover,
        .category-btn.active {
            background-color: #eacb5f;
            color: white;
            border-color: #eacb5f;
        }
    </style>
</head>
<body>
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

    <!-- JS for fetching products -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        fetchProducts();

        function fetchProducts() {
            fetch("http://localhost:3001/api/products") // Adjust URL to match your backend
                .then(response => response.json())
                .then(data => {
                    displayProducts(data);
                })
                .catch(error => console.error("Error fetching products:", error));
        }

        function displayProducts(products) {
            const container = document.getElementById("productsContainer");
            container.innerHTML = "";

            products.forEach(product => {
                const card = document.createElement("div");
                card.style.background = "#fff";
                card.style.borderRadius = "10px";
                card.style.padding = "20px";
                card.style.boxShadow = "0 2px 10px rgba(0,0,0,0.05)";
                card.innerHTML = `
                    <div style="height: 150px; background-color: #f0f0f0; border-radius: 8px; margin-bottom: 10px;"></div>
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">${product.name}</h3>
                    <p style="font-size: 14px; color: #999;">₱${product.price}</p>
                    <button style="margin-top: 10px; padding: 10px 16px; background-color: #7f4af1; color: white; border: none; border-radius: 6px; cursor: pointer;">Add to Cart</button>
                `;
                container.appendChild(card);
            });
        }

        // Category filter logic can be implemented here
        const categoryButtons = document.querySelectorAll(".category-btn");
        categoryButtons.forEach(button => {
            button.addEventListener("click", () => {
                categoryButtons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");
                // TODO: Add filtered fetch logic here
            });
        });
    });
    </script>

</body>
</html>

<?php include('includes/footer.php'); ?>
