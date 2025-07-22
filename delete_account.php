<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Logout and redirect
    session_destroy();
    header("Location: index.php");
    exit();
} else {
    header("Location: User.php");
    exit();
}
