<?php
header('Content-Type: application/json');

require_once '../../function/productfunction.php';

// Accepts either POST (preferred, cart data can be long) with a JSON body
// {"ids": ["PRO001","PRO002"]} or GET with ?ids=PRO001,PRO002
$ids = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ids = $input['ids'] ?? [];
} else {
    $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
}

$ids = array_values(array_filter(array_map('trim', $ids)));

if (empty($ids)) {
    echo json_encode([]);
    exit;
}

$productObj = new Product;
$stockMap = $productObj->getStockForIds($ids);

echo json_encode($stockMap);