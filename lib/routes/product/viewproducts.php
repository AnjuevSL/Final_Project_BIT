<?php
include_once('../../function/productfunction.php');

$proobject = new Product();

// If a specific product ID is requested, return just that one; otherwise return all
if (isset($_GET['productId'])) {
    $result = $proobject->getProductById($_GET['productId']);
} else {
    $result = $proobject->getAllProducts();
}

header('Content-Type: application/json');
echo json_encode($result);