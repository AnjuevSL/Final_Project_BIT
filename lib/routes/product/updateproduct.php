<?php
include_once('../../function/productfunction.php');

header('Content-Type: application/json');

$productId = $_POST['productId'] ?? '';
$productname = $_POST['productname'] ?? '';
$details = $_POST['details'] ?? '';
$price = $_POST['price'] ?? 0;
$category = $_POST['category'] ?? '';
$supplier = $_POST['supplier'] ?? '';

// Image is optional on update — only present if the admin chose a new file
$productimageName = $_FILES['formFile']['name'] ?? null;
$productimageType = $_FILES['formFile']['type'] ?? null;
$productimageLocation = $_FILES['formFile']['tmp_name'] ?? null;

$proobject = new Product();

$result = $proobject->updateproduct($productId, $productname, $details, $price, $category, $supplier, $productimageName, $productimageType, $productimageLocation);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update the product']);
}