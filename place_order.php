<?php
require_once 'lib/function/orderfunction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

$customer = [
    'cusid'          => trim($_POST['cusid'] ?? ''),
    'fullname'       => trim($_POST['fullname'] ?? ''),
    'phone'          => trim($_POST['phone'] ?? ''),
    'email'          => trim($_POST['email'] ?? ''),
    'address'        => trim($_POST['address'] ?? ''),
    'city'           => trim($_POST['city'] ?? ''),
    'postal_code'    => trim($_POST['postal_code'] ?? ''),
    'notes'          => trim($_POST['notes'] ?? ''),
    'payment_method' => trim($_POST['payment_method'] ?? 'cod'),
];

$errors = [];

if ($customer['fullname'] === '') $errors[] = 'Full name is required';
if ($customer['phone'] === '')    $errors[] = 'Phone number is required';
if (!filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if ($customer['address'] === '') $errors[] = 'Delivery address is required';
if ($customer['city'] === '')    $errors[] = 'City is required';

// If bank transfer selected, slip upload is mandatory
$slipFile = null;
if ($customer['payment_method'] === 'bank_transfer') {
    if (!isset($_FILES['payment_slip']) || $_FILES['payment_slip']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please upload your bank transfer slip';
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $fileType = mime_content_type($_FILES['payment_slip']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Slip must be an image (jpg/png) or PDF file';
        } elseif ($_FILES['payment_slip']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = 'Slip file is too large (max 5MB)';
        } else {
            $slipFile = $_FILES['payment_slip'];
        }
    }
}

$cart = json_decode($_POST['cart_data'] ?? '[]', true);

if (!is_array($cart) || empty($cart)) {
    $errors[] = 'Your cart is empty';
}

if (!empty($errors)) {
    $errorMsg = urlencode(implode(', ', $errors));
    header("Location: checkout.php?error={$errorMsg}");
    exit;
}

// Place the order (this also validates stock and deducts it atomically — see orderfunction.php)
$orderObj = new Order;
$result = $orderObj->placeOrder($customer, $cart, 350.00);

if ($result['status'] === 'success') {

    // If bank transfer, save the slip and link it to the order
    if ($customer['payment_method'] === 'bank_transfer' && $slipFile !== null) {
        $uploadResult = $orderObj->uploadPaymentSlip($result['orderid'], $slipFile);

        if ($uploadResult['status'] !== 'success') {
            // Order created but slip failed — still redirect, mention issue
            header('Location: order_confirmation.php?orderid=' . urlencode($result['orderid']) . '&slip_error=1');
            exit;
        }
    }

    header('Location: order_confirmation.php?orderid=' . urlencode($result['orderid']));
    exit;

} elseif ($result['status'] === 'insufficient_stock') {
    // A cart item no longer has enough stock — send the customer back to
    // checkout with a clear message instead of a generic error.
    $errorMsg = urlencode($result['message']);
    header("Location: checkout.php?error={$errorMsg}");
    exit;

} else {
    $errorMsg = urlencode($result['message']);
    header("Location: checkout.php?error={$errorMsg}");
    exit;
}