<?php
require_once '../../function/orderfunction.php';
header('Content-Type: application/json');

$ordObj = new Order();
$result = $ordObj->getAllPaymentSlips();

echo json_encode($result);