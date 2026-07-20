<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../login.php');
    exit();
}

include_once('../function/reportfunction.php');

$repObj = new Report();

// Same filters as order_report.php so the export matches exactly what's on screen
$status = $_GET['status'] ?? null;
$from   = $_GET['from'] ?? null;
$to     = $_GET['to'] ?? null;

$orders = $repObj->getOrderReport($status, $from, $to);

// Tell the browser this is an Excel-compatible file
$filename = 'order_report_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM so Excel reads UTF-8 characters (Sinhala names etc.) correctly
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Header row
fputcsv($output, ['Order ID', 'Customer Name', 'Phone', 'City', 'Payment Method', 'Subtotal', 'Delivery Fee', 'Total', 'Status', 'Date']);

// Data rows
foreach ($orders as $order) {
    fputcsv($output, [
        $order['orderid'],
        $order['customer_name'],
        $order['phone'],
        $order['city'],
        ucwords($order['payment_method']),
        number_format($order['subtotal'], 2),
        number_format($order['delivery_fee'], 2),
        number_format($order['total'], 2),
        ucwords(str_replace('_', ' ', $order['order_status'])),
        date('Y-m-d H:i', strtotime($order['created_at'])),
    ]);
}

fclose($output);
exit();
