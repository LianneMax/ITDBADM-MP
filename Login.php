<?php
// You can handle form submissions here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login/Register</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
        }
        h1 {
            margin-bottom: 10px;
            font-size: 24px;
            text-align: center;
        }
        p {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .btn-group {
            display: flex;
            margin-bottom: 20px;
        }
        .btn-group button {
            flex: 1;
            padding: 12px;
            border: none;
            cursor: pointer;
            background-color: #ddd;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-group button.active {
            background-color: #ffc107;
        }
        .form-wrapper {
            position: relative;
        }
        form {
            display: none;
            flex-direction: column;
            width: 100%;
        }
        form.active {
            display: flex;
        }
        label {
            margin-top: 15px;
            font-weight: bold;
            font-size: 13px;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="tel"],
        select {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            width: 100%;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* space for the eye icon */
        }
        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
        .name-row {
            display: flex;
            gap: 15px;
        }
        .name-row > div {
            flex: 1;
        }
        .terms {
            margin-top: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .terms label {
            display: flex;
            align-items: center;
            margin: 0;
        }
        .terms input[type="checkbox"] {
            margin-right: 8px;
            width: auto;
        }
        .terms a {
            font-size: 13px;
            color: #007bff;
            text-decoration: none;
        }
        .register-terms {
            margin-top: 20px;
            font-size: 13px;
        }
        .register-terms label {
            display: flex;
            align-items: center;
            margin: 0;
        }
        .register-terms input[type="checkbox"] {
            margin-right: 8px;
            width: auto;
        }
        .register-terms a {
            color: #ffc107;
            text-decoration: none;
        }
        button[type="submit"] {
            margin-top: 25px;
            padding: 12px;
            background-color: #ffc107;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome</h1>
    <p>Sign in to your account or create a new one</p>

    <div class="btn-group">
        <button id="loginBtn" onclick="toggleForm('login')">Login</button>
        <button id="registerBtn" class="active" onclick="toggleForm('register')">Register</button>
    </div>

    <div class="form-wrapper">
        <!-- Login Form -->
        <form id="loginForm" method="post" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email">

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Enter your password">
                <i onclick="togglePassword('password')" class="fa fa-eye"></i>
            </div>

            <div class="terms">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit">Sign In</button>
        </form>

        <!-- Register Form -->
        <form id="registerForm" method="post" action="">
            <div class="name-row">
                <div>
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="John">
                </div>
                <div>
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Doe">
                </div>
            </div>

            <label for="regEmail">Email</label>
            <input type="email" id="regEmail" name="regEmail" placeholder="john.doe@example.com">

            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" placeholder="+1 234 567 8900">

            <label for="regPassword">Password</label>
            <div class="password-container">
                <input type="password" id="regPassword" name="regPassword" placeholder="Create a password">
                <i onclick="togglePassword('regPassword')" class="fa fa-eye"></i>
            </div>

            <label for="confirmPassword">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password">
                <i onclick="togglePassword('confirmPassword')" class="fa fa-eye"></i>
            </div>

            <label for="accountType">Account Type</label>
            <select id="accountType" name="accountType">
                <option value="">Select account type</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <div class="register-terms">
                <label><input type="checkbox" name="terms"> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="submit">Create Account</button>
        </form>
    </div>
</div>

<script>
    function toggleForm(form) {
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        // Remove active class from all forms
        loginForm.classList.remove('active');
        registerForm.classList.remove('active');

        if (form === 'login') {
            loginForm.classList.add('active');
            loginBtn.classList.add('active');
            registerBtn.classList.remove('active');
        } else {
            registerForm.classList.add('active');
            loginBtn.classList.remove('active');
            registerBtn.classList.add('active');
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // Initialize default view - show register form to match the images
    toggleForm('register');
</script>

<!-- Font Awesome for eye icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>