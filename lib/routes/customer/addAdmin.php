<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$email = $_POST['adminEmail'];
$password = $_POST['adminPassword'];

$result = $customerObj->addAdmin($email, $password);

echo($result);

?>