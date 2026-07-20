<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../login.php');
    exit();
}

include_once('../function/reportfunction.php');

$repObj = new Report();

$status = $_GET['status'] ?? null;

$categories = $repObj->getCategoryReport($status);

$filename = 'category_report_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, ['Category ID', 'Category Name', 'Description', 'Products Count', 'Status', 'Created At']);

foreach ($categories as $cat) {
    fputcsv($output, [
        $cat['categoryid'],
        $cat['categoryName'],
        $cat['description'],
        (int) $cat['product_count'],
        $cat['d_status'] == 1 ? 'Active' : 'Inactive',
        date('Y-m-d', strtotime($cat['created_at'])),
    ]);
}

fclose($output);
exit();
