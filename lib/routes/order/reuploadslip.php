<?php
require_once '../../function/orderfunction.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$orderid = $_POST['orderid'] ?? '';

if ($orderid === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing order ID']);
    exit;
}

if (!isset($_FILES['payment_slip']) || $_FILES['payment_slip']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a file to upload']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
$fileType = mime_content_type($_FILES['payment_slip']['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['status' => 'error', 'message' => 'Slip must be an image (jpg/png) or PDF file']);
    exit;
}

if ($_FILES['payment_slip']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'File is too large (max 5MB)']);
    exit;
}

$orderObj = new Order;
$result = $orderObj->reuploadPaymentSlip($orderid, $_FILES['payment_slip']);

echo json_encode($result);