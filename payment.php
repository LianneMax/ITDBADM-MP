<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .checkout-header {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            gap: 16px;
        }

        .back-button {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        .back-button:hover {
            color: #374151;
        }

        .back-button svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }

        .checkout-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            gap: 12px;
        }

        .card-icon {
            width: 20px;
            height: 20px;
            color: #374151;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
        }

        /* Payment Method Styles */
        .payment-methods {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }

        .payment-method {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            gap: 8px;
        }

        .payment-method.active {
            border-color: #3b82f6;
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .payment-method:hover:not(.active) {
            border-color: #d1d5db;
            background-color: #f9fafb;
        }

        .payment-method svg {
            width: 16px;
            height: 16px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-sizing: border-box; /* Add this line */
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px; /* Changed from 16px */
        }

        .form-row-three {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px; /* Changed from 16px */
        }

        .info-text {
            background-color: #f3f4f6;
            color: #6b7280;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-top: 16px;
        }

        /* Order Summary Styles */
        .order-summary {
            position: sticky;
            top: 40px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 14px;
        }

        .summary-item:not(:last-child) {
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-item.total {
            font-weight: 600;
            font-size: 16px;
            color: #1a202c;
            padding-top: 16px;
            border-top: 2px solid #e5e7eb;
            margin-top: 8px;
        }

        .summary-label {
            color: #6b7280;
        }

        .summary-value {
            color: #1a202c;
            font-weight: 500;
        }

        .summary-buttons {
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .complete-button {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #92400e;
            font-weight: 600;
            font-size: 14px;
            padding: 16px 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .complete-button:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(217, 119, 6, 0.2);
        }

        .secure-checkout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #8b5cf6;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .secure-checkout svg {
            width: 12px;
            height: 12px;
        }

        /* Payment Method Specific Styles */
        .payment-content {
            display: none;
        }

        .payment-content.active {
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .checkout-content {
                grid-template-columns: 1fr;
                gap: 32px;
            }
            
            .order-summary {
                position: static;
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 20px 16px;
            }
            
            .card {
                padding: 24px;
            }
            
            .payment-methods {
                flex-direction: column;
            }
            
            .form-row, .form-row-three {
                grid-template-columns: 1fr;
            }
            
            .checkout-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <!-- Header -->
        <div class="checkout-header">
            <a href="#" class="back-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H6m0 0l6-6m-6 6l6 6"/>
                </svg>
                Back to Shop
            </a>
            <h1 class="checkout-title">Checkout</h1>
        </div>

        <div class="checkout-content">
            <div class="main-content">
                <!-- Payment Method Card -->
                <div class="card">
                    <div class="card-header">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <h2 class="card-title">Payment Method</h2>
                    </div>

                    <div class="payment-methods">
                        <div class="payment-method active" data-method="card">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Card
                        </div>
                        <div class="payment-method" data-method="ewallet">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20m8-10a8 8 0 1 0-16 0 8 8 0 0 0 16 0z"></path>
                            </svg>
                            E-Wallet
                        </div>
                        <div class="payment-method" data-method="cash">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 1v6m0 0V1m0 6v6m0-6l4.24-4.24M12 7L7.76 2.76M12 13v8m0 0v-8m0 8l4.24-4.24M12 21l-4.24-4.24"></path>
                            </svg>
                            Cash
                        </div>
                    </div>

                    <!-- Card Payment Content -->
                    <div class="payment-content active" id="card-content">
                        <div class="form-group">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-input" placeholder="1234 5678 9012 3456" id="card-number">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Cardholder Name</label>
                            <input type="text" class="form-input" placeholder="John Doe" id="cardholder-name">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-input" placeholder="MM/YY" id="expiry-date">
                            </div>
                            <div class="form-group">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-input" placeholder="123" id="cvv">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Billing Address</label>
                            <input type="text" class="form-input" placeholder="123 Main St, City, State 12345" id="billing-address">
                        </div>
                    </div>

                    <!-- E-Wallet Payment Content -->
                    <div class="payment-content" id="ewallet-content">
                        <div class="form-group">
                            <label class="form-label">E-Wallet Provider</label>
                            <select class="form-select" id="ewallet-provider">
                                <option value="">Select your e-wallet</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                                <option value="grabpay">GrabPay</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone Number / Account</label>
                            <input type="text" class="form-input" placeholder="+63 912 345 6789" id="ewallet-account">
                        </div>
                        
                        <div class="info-text">
                            You will be redirected to your e-wallet app to complete the payment.
                        </div>
                    </div>

                    <!-- Cash Payment Content -->
                    <div class="payment-content" id="cash-content">
                        <div class="form-group">
                            <label class="form-label">Pickup Location</label>
                            <select class="form-select" id="pickup-location">
                                <option value="">Select pickup location</option>
                                <option value="main-branch">Main Branch - BGC</option>
                                <option value="makati-branch">Makati Branch</option>
                                <option value="ortigas-branch">Ortigas Branch</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Preferred Pickup Date</label>
                            <input type="date" class="form-input" id="pickup-date">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Preferred Pickup Time</label>
                            <select class="form-select" id="pickup-time">
                                <option value="">Select time slot</option>
                                <option value="9-11">9:00 AM - 11:00 AM</option>
                                <option value="11-1">11:00 AM - 1:00 PM</option>
                                <option value="1-3">1:00 PM - 3:00 PM</option>
                                <option value="3-5">3:00 PM - 5:00 PM</option>
                            </select>
                        </div>
                        
                        <div class="info-text">
                            Please bring exact change when picking up your order. A confirmation will be sent to your email.
                        </div>
                    </div>
                </div>

                <!-- Shipping Information Card -->
                <div class="card" id="shipping-card">
                    <div class="card-header">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                        </svg>
                        <h2 class="card-title">Shipping Information</h2>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-input" placeholder="John" id="first-name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-input" placeholder="Doe" id="last-name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-input" placeholder="Street address" id="address">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="form-input" placeholder="Las Piñas" id="city">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-input" placeholder="1740" id="postal-code">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-input" placeholder="+63 912 345 6789" id="phone">
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="order-summary">
                <div class="card">
                    <div class="card-header">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4m-4 0V9a2 2 0 0 1 2-2v0a2 2 0 0 1 2 2v2m-4 0h4"></path>
                        </svg>
                        <h2 class="card-title">Order Summary</h2>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">₱0</span>
                    </div>
                    
                    <div class="summary-item" id="shipping-row" style="display: none;">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value">₱15</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">Tax</span>
                        <span class="summary-value">₱0</span>
                    </div>

                    <div class="summary-item total">
                        <span>Total</span>
                        <span id="total-amount">₱0</span>
                    </div>

                    <div class="summary-buttons">
                        <button class="complete-button" id="complete-payment">
                            <span id="button-text">Confirm Order</span>
                        </button>
                        <div class="secure-checkout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <circle cx="12" cy="16" r="1"></circle>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Secure Checkout
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Payment method switching
        const paymentMethods = document.querySelectorAll('.payment-method');
        const paymentContents = document.querySelectorAll('.payment-content');
        const buttonText = document.getElementById('button-text');
        const shippingRow = document.getElementById('shipping-row');
        const totalAmount = document.getElementById('total-amount');

        paymentMethods.forEach(method => {
            method.addEventListener('click', () => {
                // Remove active class from all methods
                paymentMethods.forEach(m => m.classList.remove('active'));
                paymentContents.forEach(c => c.classList.remove('active'));

                // Add active class to clicked method
                method.classList.add('active');
                const methodType = method.getAttribute('data-method');
                document.getElementById(`${methodType}-content`).classList.add('active');

                // Update button text and shipping
                updateOrderSummary(methodType);
            });
        });

        function updateOrderSummary(paymentMethod) {
            const subtotal = 0;
            let shipping = 0;
            let buttonTextValue = 'Confirm Order';
            const shippingCard = document.getElementById('shipping-card');

            if (paymentMethod === 'cash') {
                buttonTextValue = 'Confirm Order';
                shippingCard.style.display = 'none'; // Hide shipping card
                shippingRow.style.display = 'none';
            } else if (paymentMethod === 'ewallet') {
                shipping = 15;
                buttonTextValue = 'Complete Payment';
                shippingCard.style.display = 'block'; // Show shipping card
                shippingRow.style.display = 'flex';
            } else if (paymentMethod === 'card') {
                shipping = 15;
                buttonTextValue = 'Complete Payment';
                shippingCard.style.display = 'block'; // Show shipping card
                shippingRow.style.display = 'flex';
            }

            const total = subtotal + shipping;
            totalAmount.textContent = `₱${total}`;
            buttonText.textContent = buttonTextValue;
        }

        // Form validation and completion
        document.getElementById('complete-payment').addEventListener('click', () => {
            const activeMethod = document.querySelector('.payment-method.active').getAttribute('data-method');
            
            // Basic validation
            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const address = document.getElementById('address').value;
            
            // Payment method specific validation
if (activeMethod === 'card' || activeMethod === 'ewallet') {
    // Only validate shipping info for card and ewallet
    if (!firstName || !lastName || !address) {
        alert('Please fill in all shipping information fields.');
        return;
    }
}

if (activeMethod === 'card') {
    const cardNumber = document.getElementById('card-number').value;
    const cardholderName = document.getElementById('cardholder-name').value;
    const expiryDate = document.getElementById('expiry-date').value;
    const cvv = document.getElementById('cvv').value;

        if (!cardNumber || !cardholderName || !expiryDate || !cvv) {
            alert('Please fill in all card information.');
            return;
        }
        } else if (activeMethod === 'ewallet') {
            const provider = document.getElementById('ewallet-provider').value;
            const account = document.getElementById('ewallet-account').value;

            if (!provider || !account) {
                alert('Please select an e-wallet provider and enter your account.');
                return;
            }
        } else if (activeMethod === 'cash') {
            const location = document.getElementById('pickup-location').value;
            const date = document.getElementById('pickup-date').value;
            const time = document.getElementById('pickup-time').value;

            if (!location || !date || !time) {
                alert('Please fill in all pickup information.');
                return;
            }
        }

            // Simulate order completion
            alert('Order confirmed! You will receive a confirmation email shortly.');
        });

        // Auto-format card number
        document.getElementById('card-number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
            if (value.length > 16) {
                formattedValue = formattedValue.substring(0, 19);
            }
            e.target.value = formattedValue;
        });

        // Auto-format expiry date
        document.getElementById('expiry-date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Limit CVV to 3 digits
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 3);
        });

        // Set minimum pickup date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('pickup-date').min = tomorrow.toISOString().split('T')[0];

        // Initialize with card payment selected
        updateOrderSummary('card');
    </script>
</body>
</html>