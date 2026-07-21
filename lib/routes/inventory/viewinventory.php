<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include_once('../../function/inventoryfunction.php');

$inventoryObj = new Inventory();

// ?lowstock=1 -> only products at/below their reorder level
if (isset($_GET['lowstock']) && $_GET['lowstock'] == '1') {
    $items = $inventoryObj->getLowStockProducts();
    echo json_encode($items);
    exit;
}

// ?productId=... -> stock info for a single product
if (isset($_GET['productId']) && $_GET['productId'] !== '') {
    $item = $inventoryObj->getStockByProductId($_GET['productId']);
    echo json_encode($item);
    exit;
}

// Default -> full inventory list
$items = $inventoryObj->getAllInventory();
echo json_encode($items);