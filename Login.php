<?php
session_start();
include "includes/db.php";

// Initialize variables
$login_error = "";
$register_error = "";
$register_success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare statement to get user data
    $stmt = $conn->prepare("SELECT user_id, password, user_role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password, $user_role);
        $stmt->fetch();

        if ($password === $hashed_password) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = $user_role;
            
            // Use JavaScript redirect instead of header redirect
            echo "<script>
                alert('Login successful! Redirecting...');
                window.location.href = 'Index.php';
            </script>";
            exit;
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "No account found with that email.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $first_name = trim($_POST['firstName']);
    $last_name = trim($_POST['lastName']);
    $email = trim($_POST['regEmail']);
    $password = $_POST['regPassword'];
    $user_role = $_POST['accountType'];

    // Check if email already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $register_error = "Email is already registered.";
    } else {
        // Insert new user
        $insert = $conn->prepare("INSERT INTO users (user_role, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $user_role, $first_name, $last_name, $email, $password);
        
        if ($insert->execute()) {
            $register_success = "Account created successfully. You can now log in.";
            // Optional: Auto-switch to login form after successful registration
            echo "<script>
                setTimeout(function() {
                    toggleForm('login');
                }, 2000);
            </script>";
        } else {
            $register_error = "Something went wrong while creating the account. Error: " . $conn->error;
        }
        $insert->close();
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>

<div class="container">
    <h1>Welcome</h1>
    <p>Sign in to your account or create a new one</p>

    <div class="btn-group">
        <button id="loginBtn" class="active" onclick="toggleForm('login')"><i class="fa fa-sign-in"></i>Sign In</button>
        <button id="registerBtn" onclick="toggleForm('register')"><i class="fa fa-user-plus"></i>Register</button>
    </div>

    <div class="form-wrapper">
        <!-- Login Form -->
        <form id="loginForm" method="post" action="" class="active">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <i onclick="togglePassword('password')" class="fa fa-eye"></i>
            </div>

            <div class="terms">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>
            
            <?php if (!empty($login_error)) { echo "<p style='color: red; margin: 10px 0;'>$login_error</p>"; } ?>
            
            <button type="submit" name="login"><i class="fa fa-sign-in"></i>Sign In</button>
        </form>

        <!-- Register Form -->
        <form id="registerForm" method="post" action="">
            <div class="name-row">
                <div>
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="John" required>
                </div>
                <div>
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Doe" required>
                </div>
            </div>

            <label for="regEmail">Email</label>
            <input type="email" id="regEmail" name="regEmail" placeholder="john.doe@example.com" required>

            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" placeholder="+1 234 567 8900">

            <label for="regPassword">Password</label>
            <div class="password-container">
                <input type="password" id="regPassword" name="regPassword" placeholder="Create a password" required>
                <i onclick="togglePassword('regPassword')" class="fa fa-eye"></i>
            </div>

            <label for="confirmPassword">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                <i onclick="togglePassword('confirmPassword')" class="fa fa-eye"></i>
            </div>

            <label for="accountType">Account Type</label>
            <select id="accountType" name="accountType" required>
                <option value="">Select account type</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <div class="terms">
                <label><input type="checkbox" name="agree" required> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <?php if (!empty($register_error)) { echo "<p style='color: red; margin: 10px 0;'>$register_error</p>"; } ?>
            <?php if (!empty($register_success)) { echo "<p style='color: green; margin: 10px 0;'>$register_success</p>"; } ?>

            <button type="submit" name="register"><i class="fa fa-user-plus"></i>Create Account</button>
        </form>
    </div>
</div>

<script>
    function toggleForm(form) {
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        // Remove active class from all forms and buttons
        loginForm.classList.remove('active');
        registerForm.classList.remove('active');
        loginBtn.classList.remove('active');
        registerBtn.classList.remove('active');

        if (form === 'login') {
            loginForm.classList.add('active');
            loginBtn.classList.add('active');
        } else {
            registerForm.classList.add('active');
            registerBtn.classList.add('active');
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling;
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Password confirmation validation
    document.getElementById('confirmPassword').addEventListener('input', function() {
        const password = document.getElementById('regPassword').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    // Initialize default view - show login form
    document.addEventListener('DOMContentLoaded', function() {
        toggleForm('login');
    });
</script>

</body>
</html>