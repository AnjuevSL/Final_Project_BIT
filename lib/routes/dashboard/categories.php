<?php
header('Content-Type: application/json');
try {
    include_once('../../function/main.php');
    $obj = new Main();
    
    $sql = "SELECT category, COUNT(*) as count FROM product_tbl WHERE d_status = 1 GROUP BY category ORDER BY count DESC";
    $result = $obj->dbResult->query($sql);
    $categories = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[$row['category']] = (int)$row['count'];
        }
    }
    
    echo json_encode($categories);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>