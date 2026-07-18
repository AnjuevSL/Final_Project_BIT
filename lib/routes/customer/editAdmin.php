<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$loginid = $_POST['loginid'];
$email = $_POST['adminEmail'];
$password = $_POST['adminPassword'];

$result = $customerObj->editAdmin($loginid, $email, $password);
echo $result;
