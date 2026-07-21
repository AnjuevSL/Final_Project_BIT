<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include_once('../../function/reportfunction.php');

$category = $_GET['category'] ?? null;
$status   = $_GET['status'] ?? null;

$repObj = new Report();

$items   = $repObj->getStockReport($category, $status);
$summary = $repObj->getStockReportSummary($category, $status);

echo json_encode([
    'items'   => $items,
    'summary' => $summary,
]);