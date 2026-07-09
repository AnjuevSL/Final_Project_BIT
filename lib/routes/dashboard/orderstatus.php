<?php
include_once('../../function/db_conn.php');

header('Content-Type: application/json');

$obj = new Main();

$statuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
$statusCounts = [];

foreach ($statuses as $status) {
    $sql = "SELECT COUNT(*) as count FROM orders_tbl WHERE order_status = ?";
    $stmt = $obj->dbResult->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $statusCounts[ucwords(str_replace('_', ' ', $status))] = $row['count'];
}

echo json_encode($statusCounts);
?>