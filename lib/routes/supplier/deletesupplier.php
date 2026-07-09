<?php
include_once('../../function/supplierfunction.php');

header('Content-Type: application/json');

$supplierId = $_POST['supplierId'] ?? '';

if (empty($supplierId)) {
    echo json_encode(['status' => 'error', 'message' => 'Supplier ID is required']);
    exit;
}

$supObj = new Supplier();
$result = $supObj->deletesupplier($supplierId);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not delete the supplier']);
}