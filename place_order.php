<?php
require_once 'lib/function/orderfunction.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Collect + sanitize customer details
$customer = [
    'fullname'       => trim($_POST['fullname'] ?? ''),
    'phone'          => trim($_POST['phone'] ?? ''),
    'email'          => trim($_POST['email'] ?? ''),
    'address'        => trim($_POST['address'] ?? ''),
    'city'           => trim($_POST['city'] ?? ''),
    'postal_code'    => trim($_POST['postal_code'] ?? ''),
    'notes'          => trim($_POST['notes'] ?? ''),
    'payment_method' => trim($_POST['payment_method'] ?? 'cod'),
];

// Basic validation
$errors = [];

if ($customer['fullname'] === '') $errors[] = 'Full name is required';
if ($customer['phone'] === '')    $errors[] = 'Phone number is required';
if (!filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if ($customer['address'] === '') $errors[] = 'Delivery address is required';
if ($customer['city'] === '')    $errors[] = 'City is required';

// Decode the cart JSON sent from checkout.js
$cart = json_decode($_POST['cart_data'] ?? '[]', true);

if (!is_array($cart) || empty($cart)) {
    $errors[] = 'Your cart is empty';
}

if (!empty($errors)) {
    // Something is wrong — send the user back with the error shown
    $errorMsg = urlencode(implode(', ', $errors));
    header("Location: checkout.php?error={$errorMsg}");
    exit;
}

// Place the order
$orderObj = new Order;
$result = $orderObj->placeOrder($customer, $cart, 350.00);

if ($result['status'] === 'success') {
    header('Location: order_confirmation.php?orderid=' . urlencode($result['orderid']));
    exit;
} else {
    $errorMsg = urlencode($result['message']);
    header("Location: checkout.php?error={$errorMsg}");
    exit;
}