<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

// Get the role from GET param, default to 'customer' if not sent
$role = isset($_GET['role']) ? $_GET['role'] : 'customer';

if ($role == 'admin') {
    $result = $customerObj->loaddataadmin();
} else {
    $result = $customerObj->loaddatacus();
}

echo($result);

?>