<?php
session_start();
include_once('lib/function/main.php');
$obj = new Main();

echo "<h2>Chart Data Check</h2>";

// Check Order Status
echo "<h3>Order Status Counts:</h3>";
$statuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
foreach ($statuses as $status) {
    $sql = "SELECT COUNT(*) as count FROM orders_tbl WHERE order_status = ?";
    $stmt = $obj->dbResult->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "<p>" . ucfirst($status) . ": <strong>" . $row['count'] . "</strong></p>";
}

// Check Categories
echo "<h3>Category Counts:</h3>";
$sql = "SELECT category, COUNT(*) as count FROM product_tbl WHERE d_status = 1 GROUP BY category";
$result = $obj->dbResult->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>" . $row['category'] . ": <strong>" . $row['count'] . "</strong></p>";
    }
} else {
    echo "<p style='color: red;'><strong>❌ NO CATEGORIES FOUND!</strong></p>";
}
?>