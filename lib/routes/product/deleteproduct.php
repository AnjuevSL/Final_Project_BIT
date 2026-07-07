<?php
include_once('../../function/productfunction.php');

$productId = $_POST['productId'];

$proobject = new Product();

$result = $proobject->deleteproduct($productId);

echo ($result);