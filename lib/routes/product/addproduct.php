<?php
include_once('../../function/productfunction.php');

header('Content-Type: application/json');

$productname = $_POST['productname'] ?? '';
$details = $_POST['details'] ?? '';
$price = $_POST['price'] ?? 0;
$category = $_POST['category'] ?? '';
$supplier = $_POST['supplier'] ?? '';

// Image is required when adding a new product
if (empty($_FILES['formFile']['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product image is required']);
    exit;
}

$productimageName = $_FILES['formFile']['name'];
$productimageSize = $_FILES['formFile']['size'];
$productimageType = $_FILES['formFile']['type'];
$productimageLocation = $_FILES['formFile']['tmp_name'];

$proobject = new Product();

$result = $proobject->addproduct($productname, $details, $price, $category, $supplier, $productimageName, $productimageSize, $productimageType, $productimageLocation);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not save the product']);
}