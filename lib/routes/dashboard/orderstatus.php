<?php
header('Content-Type: application/json');
try {
    include_once('../../function/main.php');
    $obj = new Main();
    
    $statuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
    $statusCounts = [];
    
    foreach ($statuses as $status) {
        $sql = "SELECT COUNT(*) as count FROM orders_tbl WHERE order_status = ?";
        $stmt = $obj->dbResult->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $statusCounts[ucwords(str_replace('_', ' ', $status))] = (int)$row['count'];
        }
    }
    
    echo json_encode($statusCounts);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>