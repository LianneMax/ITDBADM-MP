<?php
// get_cart.php

header('Content-Type: application/json');

// Mock cart data
$cartItems = [
  [
    "id" => 1,
    "name" => "Wireless Headphones",
    "quantity" => 2,
    "price" => 1499
  ],
  [
    "id" => 2,
    "name" => "Bluetooth Mouse",
    "quantity" => 1,
    "price" => 799
  ]
];

// In production, fetch from your database like:
// $userId = $_SESSION['user_id'];
// $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
// $stmt->execute([$userId]);
// $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cartItems);
?>