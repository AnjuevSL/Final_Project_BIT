<?php
include_once('../../function/categoryfunction.php');

header('Content-Type: application/json');

$categoryId = $_POST['categoryId'] ?? '';
$categoryname = $_POST['categoryname'] ?? '';
$description = $_POST['description'] ?? '';
// $price = $_POST['price'] ?? 0;
// $category = $_POST['category'] ?? '';
// $supplier = $_POST['supplier'] ?? '';

// Image is optional on update — only present if the admin chose a new file
$categoryimageName = $_FILES['formFile']['name'] ?? null;
$categoryimageType = $_FILES['formFile']['type'] ?? null;
$categoryimageLocation = $_FILES['formFile']['tmp_name'] ?? null;

$proobject = new category();

$result = $proobject->updatecategory($categoryId, $categoryname, $description, $categoryimageName, $categoryimageType, $categoryimageLocation);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update the category']);
}