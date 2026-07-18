<?php
include_once('../../function/customerFunction.php');

$email = $_POST['customerEmail'];
$password = $_POST['customerPassword'];  
$name = $_POST['customerName'];
$phone = $_POST['customerPhone'];
$nic = $_POST['customerNIC'];
$gender = $_POST['customerGender'];
$birthday = $_POST['customerBirthday'];

$customerObj = new Customer();
$result = $customerObj->addCustomer($email, $password, $name, $phone, $nic, $gender, $birthday);

echo($result);
?>