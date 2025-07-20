<?php
session_start();
include "includes/db.php";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo "<script>console.log('PWET');</script>";
    die("Connection failed: " . $conn->connect_error);
}

// LOGIN
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
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
            header("Location: Index.php");
            exit;
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "No account found with that email.";
    }
    $stmt->close();
}

// REGISTER
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $first_name = trim($_POST['firstName']);
    $last_name = trim($_POST['lastName']);
    $email = trim($_POST['regEmail']);
    $password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT);
    $user_role = $_POST['accountType']; // user/admin

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
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Outfit', sans-serif;
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
            position: relative;
            border: 2px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            background-color: #fff;
        }
        .btn-group button {
            flex: 1;
            padding: 12px 0;
            border: none;
            cursor: pointer;
            background-color: #f5f5f5; /* Default gray background for inactive buttons */
            font-size: 14px;
            color: #666; /* Inactive button text color */
            text-align: center;
            font-family: 'Outfit', sans-serif; /* Outfit font for buttons */
            transition: all 0.3s ease;
        }
        .btn-group button.active {
            background-color: #eacb5f; /* Yellow color for active button */
            color: black; /* Black text for active button */
        }

        /* Icon styling for buttons */
        .btn-group button i {
            margin-right: 8px;
        }

        /* Yellow checkbox when checked */
        input[type="checkbox"]:checked {
            background-color: #eacb5f;
            border-color: #eacb5f;
            accent-color: #eacb5f;  /* Set checkbox checked color */
        }

        /* Forgot password text yellow */
        .terms a {
            font-size: 13px;
            color: #eacb5f; /* Yellow color for link */
            text-decoration: none;
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
            font-weight: normal;
            font-size: 13px;
        }

        /* Align First Name and Last Name fields side by side */
        .name-row {
            display: flex;
            gap: 15px;
        }
        .name-row input {
            flex: 1;
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
            font-family: 'Outfit', sans-serif;
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
            padding-right: 40px; /* Space for the eye icon */
        }

        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
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

        button[type="submit"] {
            margin-top: 30px; /* Added space */
            padding: 12px;
            background-color: #eacb5f; /* Yellow color */
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: normal;
            font-size: 14px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        button[type="submit"] i {
            margin-right: 8px;
        }
    </style>
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
