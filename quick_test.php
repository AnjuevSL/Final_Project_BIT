<?php
session_start();
include_once('lib/function/main.php');

$obj = new Main();

echo "<h2>Database Status Check</h2>";

// Check Orders
$result = $obj->dbResult->query("SELECT COUNT(*) as count FROM orders_tbl");
$orders_count = $result->fetch_assoc()['count'];
echo "<p><strong>Orders in DB:</strong> " . $orders_count . "</p>";

// Check Products
$result = $obj->dbResult->query("SELECT COUNT(*) as count FROM product_tbl");
$products_count = $result->fetch_assoc()['count'];
echo "<p><strong>Products in DB:</strong> " . $products_count . "</p>";

// Check Customers
$result = $obj->dbResult->query("SELECT COUNT(*) as count FROM customer_tbl");
$customers_count = $result->fetch_assoc()['count'];
echo "<p><strong>Customers in DB:</strong> " . $customers_count . "</p>";

// Check recent orders
echo "<h3>Recent Orders:</h3>";
$result = $obj->dbResult->query("SELECT * FROM orders_tbl LIMIT 3");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>" . $row['orderid'] . " - " . $row['customer_name'] . " - " . $row['total'] . "</p>";
    }
} else {
    echo "<p style='color: red;'><strong>❌ NO ORDERS IN DATABASE!</strong></p>";
}
?>