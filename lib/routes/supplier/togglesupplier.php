<?php
include_once('../../function/supplierfunction.php');

header('Content-Type: application/json');

$supplierId = $_POST['supplierId'] ?? '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : -1;

if (empty($supplierId) || !in_array($status, [0, 1])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid supplier ID or status']);
    exit;
}

$supObj = new Supplier();
$result = $supObj->toggleStatus($supplierId, $status);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update supplier status']);
}