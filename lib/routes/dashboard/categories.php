<?php
include_once('../../function/db_conn.php');

header('Content-Type: application/json');

$obj = new Main();

$sql = "SELECT category, COUNT(*) as count 
        FROM product_tbl 
        WHERE d_status = 1
        GROUP BY category
        ORDER BY count DESC";

$result = $obj->dbResult->query($sql);
$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[$row['category']] = $row['count'];
    }
}

echo json_encode($categories);
?>