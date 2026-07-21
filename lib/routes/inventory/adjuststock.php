<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include_once('../../function/inventoryfunction.php');

$productId    = isset($_POST['productId']) ? trim($_POST['productId']) : '';
$movementType = isset($_POST['movementType']) ? trim($_POST['movementType']) : '';
$quantity     = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
$reason       = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$userId       = $_SESSION['user'];

if ($productId === '' || $movementType === '' || $quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid input.']);
    exit;
}

$inventoryObj = new Inventory();
$result = $inventoryObj->adjustStock($productId, $movementType, $quantity, $reason, $userId);

echo json_encode(['status' => $result]);