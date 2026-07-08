<?php
include_once('../../function/productfunction.php');

header('Content-Type: application/json');

$productId = $_POST['productId'] ?? '';

if (empty($productId)) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit;
}

$proobject = new Product();

$result = $proobject->deleteproduct($productId);



if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    // Could be a foreign key constraint error (product is referenced in orders)
    // or a general database error
    echo json_encode(['status' => 'error', 'message' => 'Could not delete the product. It may be referenced in existing orders.']);
}