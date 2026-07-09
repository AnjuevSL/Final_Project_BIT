<?php
include_once('../../function/supplierfunction.php');

header('Content-Type: application/json');

$supplierId = $_GET['supplierId'] ?? null;

$supObj = new Supplier();

if ($supplierId) {
    $result = $supObj->getSupplierById($supplierId);
    echo json_encode($result);
} else {
    $result = $supObj->getAllSuppliers();
    echo json_encode($result);
}