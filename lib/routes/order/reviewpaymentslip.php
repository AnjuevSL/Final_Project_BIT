<?php
session_start();
include_once('../../function/orderfunction.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$slipId   = $_POST['slipId'] ?? null;
$decision = $_POST['decision'] ?? null;   // 'approved' or 'rejected'
$reason   = trim($_POST['reason'] ?? '');
$adminName = $_SESSION['user'] ?? 'Admin';

if (!$slipId || !in_array($decision, ['approved', 'rejected'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid parameters']);
    exit;
}

$ordObj = new Order();
$result = $ordObj->reviewPaymentSlip($slipId, $decision, $adminName, $reason ?: null);

echo json_encode($result);