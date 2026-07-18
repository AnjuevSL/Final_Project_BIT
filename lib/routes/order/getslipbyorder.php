<?php
include_once('../../function/orderfunction.php');
header('Content-Type: application/json');

$orderid = $_GET['orderId'] ?? null;

if (!$orderid) {
    echo json_encode(['status' => 'error', 'message' => 'Missing orderId']);
    exit;
}

$ordObj = new Order();
$slip = $ordObj->getSlipByOrderId($orderid);

echo json_encode($slip ?: ['status' => 'error', 'message' => 'No slip found']);