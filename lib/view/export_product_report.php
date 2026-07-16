<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../index.php');
    exit();
}

include_once('../function/reportfunction.php');

$repObj = new Report();

$category = $_GET['category'] ?? null;
$supplier = $_GET['supplier'] ?? null;
$status   = $_GET['status'] ?? null;

$products = $repObj->getProductReport($category, $supplier, $status);

$filename = 'product_report_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, ['Product ID', 'Product Name', 'Category', 'Supplier', 'Price', 'Status']);

foreach ($products as $product) {
    fputcsv($output, [
        $product['productid'],
        $product['productName'],
        $product['categoryName'] ?? 'N/A',
        $product['supplierName'] ?? 'N/A',
        number_format($product['price'], 2),
        $product['d_status'] == 1 ? 'Active' : 'Inactive',
    ]);
}

fclose($output);
exit();
