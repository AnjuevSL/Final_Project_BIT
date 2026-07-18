<?php
include_once('../../function/orderfunction.php');

header('Content-Type: application/json');

$ordObj = new Order();
$result = $ordObj->getPendingPaymentSlips();

echo json_encode($result);