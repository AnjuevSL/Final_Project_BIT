<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../login.php');
    exit();
}

include_once('../../function/reportfunction.php');

$repObj = new Report();

$category = $_GET['category'] ?? null;
$status   = $_GET['status'] ?? null;

$items = $repObj->getStockReport($category, $status);

$filename = 'inventory_stock_report_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, ['Product ID', 'Product Name', 'Category', 'Quantity', 'Reorder Level', 'Stock Status', 'Product Status']);

foreach ($items as $item) {
    $stockStatus = $item['quantity'] <= $item['reorder_level'] ? 'Low Stock' : 'In Stock';

    fputcsv($output, [
        $item['productid'],
        $item['productName'],
        $item['categoryName'] ?? 'N/A',
        $item['quantity'],
        $item['reorder_level'],
        $stockStatus,
        $item['d_status'] == 1 ? 'Active' : 'Inactive',
    ]);
}

fclose($output);
exit();