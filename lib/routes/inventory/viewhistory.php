<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include_once('../../function/inventoryfunction.php');

$productId = isset($_GET['productId']) ? trim($_GET['productId']) : '';

if ($productId === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing productId.']);
    exit;
}

$inventoryObj = new Inventory();
$movements = $inventoryObj->getMovementHistory($productId);

echo json_encode($movements);