<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$searchtext = $_GET['searchtext'];
$role = isset($_GET['role']) ? $_GET['role'] : 'customer';

$result = $customerObj->loaddatasearch($searchtext, $role);

echo($result);

?>