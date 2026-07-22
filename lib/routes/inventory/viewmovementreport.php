<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include_once('../../function/reportfunction.php');

$dateFrom     = $_GET['dateFrom'] ?? null;
$dateTo       = $_GET['dateTo'] ?? null;
$productId    = $_GET['productId'] ?? null;
$movementType = $_GET['movementType'] ?? null;

$repObj = new Report();

$items   = $repObj->getMovementReport($dateFrom, $dateTo, $productId, $movementType);
$summary = $repObj->getMovementReportSummary($dateFrom, $dateTo, $productId, $movementType);

echo json_encode([
    'items'   => $items,
    'summary' => $summary,
]);