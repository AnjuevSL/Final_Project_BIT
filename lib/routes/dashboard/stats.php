<?php
header('Content-Type: application/json');
try {
    include_once('../../function/main.php');
    $obj = new Main();
    
    $sqlOrders = "SELECT COUNT(*) as count FROM orders_tbl";
    $totalOrders = $obj->dbResult->query($sqlOrders)->fetch_assoc()['count'] ?? 0;
    
    $sqlRevenue = "SELECT COALESCE(SUM(total), 0) as revenue FROM orders_tbl";
    $totalRevenue = $obj->dbResult->query($sqlRevenue)->fetch_assoc()['revenue'] ?? 0;
    
    $sqlProducts = "SELECT COUNT(*) as count FROM product_tbl WHERE d_status = 1";
    $totalProducts = $obj->dbResult->query($sqlProducts)->fetch_assoc()['count'] ?? 0;
    
    $sqlCustomers = "SELECT COUNT(*) as count FROM customer_tbl";
    $totalCustomers = $obj->dbResult->query($sqlCustomers)->fetch_assoc()['count'] ?? 0;
    
    echo json_encode([
        'totalOrders' => (int)$totalOrders,
        'totalRevenue' => (float)$totalRevenue,
        'totalProducts' => (int)$totalProducts,
        'totalCustomers' => (int)$totalCustomers
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>