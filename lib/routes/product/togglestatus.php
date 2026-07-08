<?php
include_once('../../function/productfunction.php');

header('Content-Type: application/json');

$productId = $_POST['productId'] ?? '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : -1;

if (empty($productId) || !in_array($status, [0, 1])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID or status']);
    exit;
}

$proobject = new Product();
$result = $proobject->toggleStatus($productId, $status);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update product status']);
}