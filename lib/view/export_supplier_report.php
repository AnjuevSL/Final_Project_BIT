<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../login.php');
    exit();
}

include_once('../function/reportfunction.php');

$repObj = new Report();

$status = $_GET['status'] ?? null;

$suppliers = $repObj->getSupplierReport($status);

$filename = 'supplier_report_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, ['Supplier ID', 'Supplier Name', 'Email', 'Phone', 'Products Supplied', 'Status', 'Created At']);

foreach ($suppliers as $sup) {
    fputcsv($output, [
        $sup['supplierid'],
        $sup['supplierName'],
        $sup['email'] ?? '-',
        $sup['phone'] ?? '-',
        (int) $sup['product_count'],
        $sup['d_status'] == 1 ? 'Active' : 'Inactive',
        date('Y-m-d', strtotime($sup['created_at'])),
    ]);
}

fclose($output);
exit();
