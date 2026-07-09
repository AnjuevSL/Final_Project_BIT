<?php
include_once('../../function/db_conn.php');

header('Content-Type: application/json');

$obj = new Main();

$sql = "SELECT orderid, customer_name, total, order_status, created_at 
        FROM orders_tbl 
        ORDER BY created_at DESC 
        LIMIT 5";

$result = $obj->dbResult->query($sql);
$orders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

echo json_encode($orders);
?>