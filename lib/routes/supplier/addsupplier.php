<?php
include_once('../../function/supplierfunction.php');

header('Content-Type: application/json');

$supplierName = $_POST['supplierName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

if (empty($supplierName)) {
    echo json_encode(['status' => 'error', 'message' => 'Supplier name is required']);
    exit;
}

$supObj = new Supplier();
$result = $supObj->addsupplier($supplierName, $email, $phone, $address);

if ($result === 'success') {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not save the supplier']);
}