<?php
include_once('../../function/categoryfunction.php');

header('Content-Type: application/json');

$categoryId = $_POST['categoryId'] ?? '';

if (empty($categoryId)) {
    echo json_encode(['status' => 'error', 'message' => 'category ID is required']);
    exit;
}

$proobject = new category();

$result = $proobject->deletecategory($categoryId);



if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    // Could be a foreign key constraint error (category is referenced in orders)
    // or a general database error
    echo json_encode(['status' => 'error', 'message' => 'Could not delete the category. It may be referenced in existing orders.']);
}