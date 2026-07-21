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
$reorderLevel = isset($_POST['reorderLevel']) ? (int) $_POST['reorderLevel'] : null;

if ($productId === '' || $reorderLevel === null || $reorderLevel < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid input.']);
    exit;
}

$inventoryObj = new Inventory();
$result = $inventoryObj->updateReorderLevel($productId, $reorderLevel);

echo json_encode(['status' => $result]);