<?php
include_once('../../function/categoryfunction.php');

header('Content-Type: application/json');

$categoryname = $_POST['categoryname'] ?? '';
$details = $_POST['details'] ?? '';
// $price = $_POST['price'] ?? 0;
// $category = $_POST['category'] ?? '';
// $supplier = $_POST['supplier'] ?? '';

// Image is required when adding a new category
if (empty($_FILES['formFile']['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'category image is required']);
    exit;
}

$categoryimageName = $_FILES['formFile']['name'];
$categoryimageSize = $_FILES['formFile']['size'];
$categoryimageType = $_FILES['formFile']['type'];
$categoryimageLocation = $_FILES['formFile']['tmp_name'];

$proobject = new category();

$result = $proobject->addcategory(
    $categoryname,
    $categoryimageName,
    $categoryimageSize,
    $categoryimageType,
    $categoryimageLocation,
    $details
);
if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not save the category']);
}
