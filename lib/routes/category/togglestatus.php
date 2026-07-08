<?php
include_once('../../function/categoryfunction.php');

header('Content-Type: application/json');

$categoryId = $_POST['categoryId'] ?? '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : -1;

if (empty($categoryId) || !in_array($status, [0, 1])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid category ID or status']);
    exit;
}

$proobject = new category();
$result = $proobject->toggleStatus($categoryId, $status);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update category status']);
}