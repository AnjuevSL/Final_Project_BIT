<?php
include_once('../../function/supplierfunction.php');

header('Content-Type: application/json');

$supplierId = $_POST['supplierId'] ?? '';
$supplierName = $_POST['supplierName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

if (empty($supplierId) || empty($supplierName)) {
    echo json_encode(['status' => 'error', 'message' => 'Supplier ID and name are required']);
    exit;
}

$supObj = new Supplier();
$result = $supObj->updatesupplier($supplierId, $supplierName, $email, $phone, $address);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update the supplier']);
}