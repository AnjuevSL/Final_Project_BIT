<?php
header('Content-Type: application/json');
try {
    include_once('../../function/main.php');
    $obj = new Main();
    
    $sql = "SELECT oi.product_name, COUNT(DISTINCT oi.orderid) as order_count, COALESCE(SUM(oi.line_total), 0) as total_revenue FROM order_items_tbl oi GROUP BY oi.product_name ORDER BY order_count DESC LIMIT 5";
    $result = $obj->dbResult->query($sql);
    $products = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>