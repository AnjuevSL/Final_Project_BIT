<?php
include_once('../../function/orderfunction.php');

header('Content-Type: application/json');

$status = $_GET['status'] ?? null;
$orderId = $_GET['orderId'] ?? null;

$ordObj = new Order();

if ($orderId) {
    // Single order with items
    $result = $ordObj->getOrderById($orderId);
    echo json_encode($result);
} elseif ($status) {
    // Orders by status
    $result = $ordObj->getOrdersByStatus($status);
    echo json_encode($result);
} else {
    // All orders
    $result = $ordObj->getAllOrders();
    echo json_encode($result);
}