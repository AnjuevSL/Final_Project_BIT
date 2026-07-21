<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../login.php');
    exit();
}

include_once('../../function/reportfunction.php');

$repObj = new Report();

$dateFrom     = $_GET['dateFrom'] ?? null;
$dateTo       = $_GET['dateTo'] ?? null;
$productId    = $_GET['productId'] ?? null;
$movementType = $_GET['movementType'] ?? null;

$movements = $repObj->getMovementReport($dateFrom, $dateTo, $productId, $movementType);

$filename = 'inventory_movement_report_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, ['Date', 'Product ID', 'Product Name', 'Type', 'Change', 'Before', 'After', 'Reason', 'By']);

foreach ($movements as $m) {
    fputcsv($output, [
        $m['created_at'],
        $m['productid'],
        $m['productName'],
        $m['movement_type'],
        $m['quantity_change'],
        $m['previous_quantity'],
        $m['new_quantity'],
        $m['reason'] ?? '',
        $m['created_by'] ?? '',
    ]);
}

fclose($output);
exit();