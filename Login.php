<?php
session_start();
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    // Database connection
    $servername = "localhost";
    $username = "root"; // Change if necessary
    $password = ""; // Change if necessary
    $dbname = "pluggedin_itdbadm";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password, user_role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password, $user_role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = $user_role;
            header("refresh:2;url=Index.php");
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
    $password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT);
    $user_role = $_POST['accountType'];

    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $register_error = "Email is already registered.";
    } else {
        $insert = $conn->prepare("INSERT INTO users (user_role, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $user_role, $first_name, $last_name, $email, $password);
        if ($insert->execute()) {
            $register_success = "Account created successfully. You can now log in.";
        } else {
            $register_error = "Something went wrong while creating the account.";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome for icons -->
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
            <?php if (isset($login_error)) { echo "<p style='color: red;'>$login_error</p>"; } ?>
            <button type="submit"><i class="fa fa-sign-in"></i>Sign In</button>
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

            <div class="terms">
                <label><input type="checkbox" name="agree"> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="submit"><i class="fa fa-user-plus"></i>Create Account</button>
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
            registerBtn.classList.remove('active');
        } else {
            registerForm.classList.add('active');
            registerBtn.classList.add('active');
            loginBtn.classList.remove('active');
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // Initialize default view - show login form
    toggleForm('login');
</script>

</body>
</html>
