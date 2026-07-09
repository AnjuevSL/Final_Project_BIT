<?php
include_once('../../function/orderfunction.php');

header('Content-Type: application/json');

$orderId = $_POST['orderId'] ?? '';
$newStatus = $_POST['status'] ?? '';

if (empty($orderId) || empty($newStatus)) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID and status are required']);
    exit;
}

$ordObj = new Order();
$result = $ordObj->updateOrderStatus($orderId, $newStatus);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update order status']);
}