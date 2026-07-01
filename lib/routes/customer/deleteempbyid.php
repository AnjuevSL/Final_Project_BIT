<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$userid = $_GET['userid'];

$result = $customerObj->deletedatabyid($userid);

echo($result);

?>