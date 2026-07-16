<?php
header('Content-Type: application/json');
try {
    include_once('../../function/main.php');
    $obj = new Main();
    
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(total), 0) as revenue FROM orders_tbl WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC";
    $result = $obj->dbResult->query($sql);
    $revenue = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $revenue[$row['month']] = (float)$row['revenue'];
        }
    }
    
    // Fill missing months
    $currentDate = new DateTime();
    $currentDate->sub(new DateInterval('P11M'));
    for ($i = 0; $i < 12; $i++) {
        $monthKey = $currentDate->format('Y-m');
        if (!isset($revenue[$monthKey])) {
            $revenue[$monthKey] = 0;
        }
        $currentDate->add(new DateInterval('P1M'));
    }
    ksort($revenue);
    
    echo json_encode($revenue);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>