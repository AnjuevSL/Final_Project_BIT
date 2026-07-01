<?php

//include the function page
include_once('../../function/customerFunction.php');

$email = $_POST['customerEmail'];
$userid = $_POST['customerid'];
$name = $_POST['customerName'];
$phone = $_POST['customerPhone'];
$nic = $_POST['customerNIC'];
$gender = $_POST['customerGender'];
$birthday = $_POST['customerBirthday'];

$customerObj = new Customer();

$result = $customerObj->editCustomer($userid, $email, $name, $phone, $nic, $gender, $birthday);

echo($result);

?>