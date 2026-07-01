<?php

//include the function page
include_once('../../function/AuthFunction.php');

$email = $_POST['loginEmail'];
$password = $_POST['loginPassword'];

$customerObj = new auth();

$result = $customerObj->authentication($email, $password);

echo($result);

?>