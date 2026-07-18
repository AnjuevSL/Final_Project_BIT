<?php
session_start();
include_once('../../function/customerFunction.php');

$email = $_POST['adminEmail'];
$password = $_POST['adminPassword'];

$customerObj = new Customer();

echo $customerObj->editCurrentAdmin($email, $password);